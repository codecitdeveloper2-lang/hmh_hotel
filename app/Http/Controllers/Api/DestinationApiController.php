<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DestinationApiController extends Controller
{
    /**
     * Return all active destinations with cities, media, and SEO metadata.
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $this->localeFromRequest($request);

        $data = Cache::tags(['destinations'])->remember(
            "destinations:index:{$locale}",
            $this->cacheTtl(),
            function () use ($locale) {
                $destinations = Destination::with([
                    'cities' => function ($q) {
                        $q->where('is_active', true)->orderBy('sort_order');
                    },
                    'seoMetadata'
                ])
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();

                return $destinations->map(fn($d) => $this->formatDestination($d, $locale))->values();
            }
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Return a single active destination by slug.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $this->localeFromRequest($request);

        $data = Cache::tags(['destinations'])->remember(
            "destinations:show:{$slug}:{$locale}",
            $this->cacheTtl(),
            function () use ($locale, $slug) {
                $destination = Destination::with([
                    'cities' => function ($q) {
                        $q->where('is_active', true)->orderBy('sort_order');
                    },
                    'seoMetadata'
                ])
                    ->where(function($query) use ($slug) {
                        $query->where('slug', $slug)
                              ->orWhere('slug', '/destinations/' . $slug)
                              ->orWhere('slug', ltrim($slug, '/'));
                    })
                    ->where('is_active', true)
                    ->firstOrFail();

                return $this->formatDestination($destination, $locale);
            }
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Format a Destination model into a clean API response array.
     */
    private function formatDestination(Destination $destination, string $locale): array
    {
        // Build full public URLs for banner images (Spatie Media Library)
        $bannerImages = $destination->getMedia('banner_images')
            ->map(fn($media) => $media->getFullUrl())
            ->values()
            ->toArray();

        // Format cities
        $cities = $destination->cities->map(function ($city) use ($locale) {
            // Build city image URL — stored as a relative path in city_image column
            $cityImageUrl = null;
            if ($city->city_image) {
                $cityImageUrl = url('storage/' . ltrim($city->city_image, '/'));
            }

            return [
                'id' => $city->id,
                'slug' => $city->slug,
                'name' => $this->translated($city, 'name', $locale),
                'description' => $this->translated($city, 'description', $locale),
                'city_image_url' => $cityImageUrl,
                'city_link' => $city->city_link,
                'layout_type' => $city->layout_type,
                'hotel_labels' => $city->hotel_labels ?? [],
                'sort_order' => $city->sort_order,
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
            ];
        })->values()->toArray();

        // SEO metadata
        $seo = null;
        if ($destination->seoMetadata) {
            $s = $destination->seoMetadata;
            $seo = [
                'meta_title' => $s->meta_title ?? null,
                'meta_description' => $s->meta_description ?? null,
                'meta_keywords' => $s->meta_keywords ?? null,
                'og_title' => $s->og_title ?? null,
                'og_description' => $s->og_description ?? null,
                'og_image' => $s->og_image ?? null,
            ];
        }

        // Map embeds formatting
        $mapEmbeds = [];
        if (is_array($destination->map_embed_code)) {
            $mapEmbeds = collect($destination->map_embed_code)
                ->pluck('embed_code')
                ->filter()
                ->values()
                ->toArray();
        }

        return [
            'id' => $destination->id,
            'slug' => $destination->slug,
            'country' => $destination->country,
            'name' => $this->translated($destination, 'name', $locale),
            'description' => $this->translated($destination, 'description', $locale),
            'is_active' => $destination->is_active,
            'sort_order' => $destination->sort_order,
            'banner_images' => $bannerImages,
            'map_embeds' => $mapEmbeds,
            'cities' => $cities,
            'seo' => $seo,
        ];
    }

    /**
     * Get a translated attribute with fallback to 'en' if the requested
     * locale is empty/missing (Spatie translatable can return '' if unset).
     */
    private function translated($model, string $field, string $locale): ?string
    {
        $value = $model->getTranslation($field, $locale, false);

        if (empty($value) && $locale !== 'en') {
            $value = $model->getTranslation($field, 'en', false);
        }

        return $value;
    }

    /**
     * Read the requested API locale. Supports X-Locale and X-Local because
     * some clients may use the shorter header name.
     */
    private function localeFromRequest(Request $request): string
    {
        $locale = $request->header('X-Locale')
            ?? $request->header('X-Local')
            ?? config('app.locale', 'en');

        return in_array($locale, ['en', 'ar'], true) ? $locale : 'en';
    }

    private function cacheTtl(): int
    {
        return (int) config('cache.destination_api_ttl', 600);
    }
}
