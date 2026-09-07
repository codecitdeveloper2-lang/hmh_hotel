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
                $slugs = ($slug === 'terms-and-conditions' || $slug === 'terms-conditions') 
                    ? ['terms-conditions', 'terms-and-conditions'] 
                    : ($slug === 'privacy-policy' || $slug === 'privacy-statement' 
                        ? ['privacy-policy', 'privacy-statement'] 
                        : ($slug === 'brands' || $slug === 'our-brands'
                            ? ['our-brands', 'brands']
                            : [$slug]));

                $page = Page::whereIn('slug', $slugs)
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

        $body = $this->resolveImages($body ?? []);

        if (is_array($body)) {
            // Provide convenient aliases for frontend consumers
            if (isset($body['team_members_list']) && !isset($body['team_list'])) {
                $body['team_list'] = $body['team_members_list'];
            }
            if (isset($body['history_timeline']) && !isset($body['timeline'])) {
                $body['timeline'] = $body['history_timeline'];
            }
        }

        return [
            'id' => $page->id,
            'slug' => $page->slug,
            'page_type' => $page->page_type,
            'title' => $this->translated($page, 'title', $locale),
            'body' => $body,
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
     * Recursively resolve all image paths and URLs in the given data.
     */
    private function resolveImages($data, ?string $parentKey = null)
    {
        if (is_array($data)) {
            $isAssoc = !array_is_list($data);
            $result = [];

            foreach ($data as $key => $value) {
                $currentKey = is_string($key) ? $key : $parentKey;
                $result[$key] = $this->resolveImages($value, $currentKey);
            }

            // For associative arrays (e.g. brand items, slide items), provide convenience keys
            if ($isAssoc) {
                if (isset($result['image']) && is_string($result['image']) && trim($result['image']) !== '') {
                    $result['image_url'] = $result['image'];
                }
                if (isset($result['logo']) && is_string($result['logo']) && trim($result['logo']) !== '') {
                    $result['logo_url'] = $result['logo'];
                }
                if (isset($result['icon']) && is_string($result['icon']) && trim($result['icon']) !== '') {
                    $result['icon_url'] = $result['icon'];
                }
            }

            return $result;
        }

        if (is_string($data)) {
            $trimmed = trim($data);
            if ($trimmed === '') {
                return $data;
            }

            // If string contains HTML with <img> tags, resolve their src attributes
            if (str_contains($data, '<img')) {
                return $this->resolveHtmlImages($data);
            }

            // If the key indicates an image or the value looks like an image filename/path
            if ($this->isImageKey($parentKey) || $this->isImagePath($trimmed)) {
                return $this->formatImageUrl($trimmed);
            }
        }

        return $data;
    }

    /**
     * Check if a key name suggests an image field.
     */
    private function isImageKey(?string $key): bool
    {
        if (empty($key)) {
            return false;
        }

        $key = strtolower($key);

        $exactKeys = [
            'image', 'images', 'banner_image', 'banner_images', 'cover_image',
            'hero_image', 'hero_images', 'logo', 'logos', 'footer_logo',
            'icon', 'icons', 'thumbnail', 'thumbnails', 'photo', 'photos',
            'picture', 'pictures', 'expansion_image', 'our_vision_image',
            'our_mission_image', 'image_url', 'logo_url', 'icon_url',
        ];

        if (in_array($key, $exactKeys, true)) {
            return true;
        }

        return str_ends_with($key, '_image')
            || str_ends_with($key, '_images')
            || str_ends_with($key, '_logo')
            || str_ends_with($key, '_icon')
            || str_ends_with($key, '_photo');
    }

    /**
     * Check if a string looks like an image filename or path.
     */
    private function isImagePath(string $value): bool
    {
        // Must not contain HTML or newlines
        if (str_contains($value, '<') || str_contains($value, "\n")) {
            return false;
        }

        // Must end with an image file extension
        return (bool) preg_match('/\.(jpe?g|png|gif|svg|webp|bmp|avif|ico)(\?.*)?$/i', $value);
    }

    /**
     * Format an image filename or relative path into a full URL.
     */
    private function formatImageUrl(string $path): string
    {
        $path = trim($path);

        if (empty($path)) {
            return $path;
        }

        // If it's already an absolute URL (http://, https://)
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Protocol-relative URL
        if (str_starts_with($path, '//')) {
            return 'https:' . $path;
        }

        // Stored in storage directory
        if (str_starts_with($path, 'storage/') || str_starts_with($path, '/storage/')) {
            $clean = preg_replace('#^/?storage/#', '', $path);
            return url('storage/' . ltrim($clean, '/'));
        }

        // Uploads disk or relative path
        $clean = preg_replace('#^/?uploads/#', '', $path);
        return url('uploads/' . ltrim($clean, '/'));
    }

    /**
     * Replace src attributes in HTML content with full image URLs.
     */
    private function resolveHtmlImages(string $html): string
    {
        return preg_replace_callback('/(<img\b[^>]*?\bsrc=["\'])([^"\']+)(["\'][^>]*>)/i', function ($matches) {
            $prefix = $matches[1];
            $src = $matches[2];
            $suffix = $matches[3];

            return $prefix . $this->formatImageUrl($src) . $suffix;
        }, $html);
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
