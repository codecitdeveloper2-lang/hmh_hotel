<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageApiController extends Controller
{
    /**
     * Return all active group-level pages (property_id is null).
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $this->localeFromRequest($request);

        $data = Cache::remember(
            "pages:index:{$locale}",
            $this->cacheTtl(),
            function () use ($locale) {
                $pages = Page::where('is_active', true)
                    ->whereNull('property_id')
                    ->get();

                return $pages->map(fn($p) => $this->formatPage($p, $locale))->values();
            }
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Return a single active group-level page by slug.
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $this->localeFromRequest($request);

        $data = Cache::remember(
            "pages:show:{$slug}:{$locale}",
            $this->cacheTtl(),
            function () use ($locale, $slug) {
                $page = Page::where('slug', $slug)
                    ->where('is_active', true)
                    ->whereNull('property_id')
                    ->firstOrFail();

                return $this->formatPage($page, $locale);
            }
        );

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Format a Page model into a clean API response array.
     */
    private function formatPage(Page $page, string $locale): array
    {
        $bodyRaw = $this->translated($page, 'body', $locale);
        $body = is_string($bodyRaw) ? json_decode($bodyRaw, true) : $bodyRaw;

        return [
            'id' => $page->id,
            'slug' => $page->slug,
            'page_type' => $page->page_type,
            'title' => $this->translated($page, 'title', $locale),
            'body' => $body ?? [],
            'seo' => [
                'meta_title' => $this->translated($page, 'meta_title', $locale),
                'meta_description' => $this->translated($page, 'meta_description', $locale),
            ],
            'is_active' => $page->is_active,
            'created_at' => $page->created_at,
            'updated_at' => $page->updated_at,
        ];
    }

    /**
     * Get a translated attribute with fallback to 'en'.
     */
    private function translated($model, string $field, string $locale)
    {
        $value = $model->getTranslation($field, $locale, false);

        if (empty($value) && $locale !== 'en') {
            $value = $model->getTranslation($field, 'en', false);
        }

        return $value;
    }

    /**
     * Read the requested API locale.
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
        return (int) config('cache.page_api_ttl', 600);
    }
}
