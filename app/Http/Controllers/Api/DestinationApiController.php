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
                $rawImage = str_replace('storage/', 'uploads/', $city->city_image);
                if (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://')) {
                    $cityImageUrl = $rawImage;
                } else {
                    $cityImageUrl = url('uploads/' . ltrim(str_replace('uploads/', '', ltrim($rawImage, '/')), '/'));
                }
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

        // Matching hotels in this destination
        $cityNames = $destination->cities->pluck('name')->toArray();
        $hotels = \App\Models\Property::where('type', 'hotel')
            ->where(function ($q) use ($destination, $cityNames) {
                $q->where('country', $destination->country)
                  ->orWhere('country', 'like', '%' . $destination->country . '%');
                if (!empty($cityNames)) {
                    $q->orWhereIn('city', $cityNames);
                }
            })
            ->get()
            ->map(function ($h) {
                $hotelName = is_array($h->name) ? ($h->name['en'] ?? '') : $h->name;
                $coverImage = $h->getFirstMediaUrl('cover_image')
                    ?: ($h->cover_image ? url('uploads/' . ltrim($h->cover_image, '/')) : null);
                return [
                    'id'              => $h->id,
                    'name'            => $hotelName,
                    'slug'            => $h->slug,
                    'cover_image'     => $coverImage,
                    'latitude'        => $h->latitude ? (float) $h->latitude : null,
                    'longitude'       => $h->longitude ? (float) $h->longitude : null,
                    'address'         => $h->address,
                    'city'            => $h->city,
                    'country'         => $h->country,
                    'phone'           => $h->phone,
                    'email'           => $h->email,
                    'google_location' => $h->google_location,
                    'star_rating'     => $h->star_rating,
                ];
            })
            ->values()
            ->toArray();

        // If no hotels found in this destination, provide fallback location from city or map_embed_code
        if (empty($hotels)) {
            $fallbackLat = null;
            $fallbackLng = null;
            $fallbackName = $destination->name;

            // 1. Try city coordinates
            foreach ($destination->cities as $city) {
                if ($city->latitude && $city->longitude) {
                    $fallbackLat = (float) $city->latitude;
                    $fallbackLng = (float) $city->longitude;
                    $fallbackName = $this->translated($city, 'name', $locale) ?: $city->name;
                    break;
                }
            }

            // 2. Try parsing from map_embed_code
            if (!$fallbackLat && !empty($destination->map_embed_code)) {
                foreach ($destination->map_embed_code as $embed) {
                    $code = is_array($embed) ? ($embed['embed_code'] ?? '') : (string) $embed;
                    if (preg_match('/!2d([-\d\.]+)!3d([-\d\.]+)/', $code, $matches)) {
                        $fallbackLng = (float) $matches[1];
                        $fallbackLat = (float) $matches[2];
                        break;
                    }
                    if (preg_match('/[?&]q=([-\d\.]+),([-\d\.]+)/', $code, $matches)) {
                        $fallbackLat = (float) $matches[1];
                        $fallbackLng = (float) $matches[2];
                        break;
                    }
                }
            }

            // 3. Known country coordinate fallbacks
            if (!$fallbackLat) {
                $knownCoords = [
                    'sudan' => ['lat' => 15.5007, 'lng' => 32.5599, 'city' => 'Khartoum'],
                ];
                $slugKey = strtolower(trim($destination->slug, '/'));
                if (isset($knownCoords[$slugKey])) {
                    $fallbackLat = $knownCoords[$slugKey]['lat'];
                    $fallbackLng = $knownCoords[$slugKey]['lng'];
                    $fallbackName = $knownCoords[$slugKey]['city'];
                }
            }

            if ($fallbackLat && $fallbackLng) {
                $firstCity = $destination->cities->first();
                $firstCityName = $firstCity ? ($this->translated($firstCity, 'name', $locale) ?: $firstCity->name) : $fallbackName;
                $destName = $this->translated($destination, 'name', $locale) ?: $destination->name;
                $hotels = [[
                    'id'              => 9000 + $destination->id,
                    'name'            => $firstCityName . ', ' . $destName,
                    'slug'            => $destination->slug,
                    'cover_image'     => $bannerImages[0] ?? null,
                    'latitude'        => $fallbackLat,
                    'longitude'       => $fallbackLng,
                    'address'         => $firstCityName . ', ' . ($destination->country ?: $destName),
                    'city'            => $firstCityName,
                    'country'         => $destination->country ?: $destName,
                    'phone'           => null,
                    'email'           => null,
                    'google_location' => "https://maps.google.com/?q={$fallbackLat},{$fallbackLng}",
                    'star_rating'     => null,
                ]];
            }
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
            'hotels' => $hotels,
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
