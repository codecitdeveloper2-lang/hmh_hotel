<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferApiController extends Controller
{
    /**
     * Get distinct offer types.
     */
    public function getTypes(): JsonResponse
    {
        $types = Offer::whereNotNull('offer_type')
            ->where('offer_type', '!=', '')
            ->distinct()
            ->pluck('offer_type')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => array_values(array_unique($types))
        ]);
    }

    /**
     * Display a listing of active offers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Offer::orderBy('sort_order', 'asc');

        if (!$request->has('all') || $request->query('all') !== 'true') {
            // Get strictly active offers
            $query->where('is_active', 1)->where('status', 'Active');
        }

        $offers = $query->get();

        // Eager load all related properties with their parent brand in a single query to eliminate N+1
        $propertyIds = $offers->pluck('hotel')->filter()->unique();
        $properties = Property::with('parent')->whereIn('id', $propertyIds)->get()->keyBy('id');

        // Transform the offers using consistent formatting
        $transformed = $offers->map(function ($offer) use ($properties) {
            $property = $properties->get($offer->hotel);
            return $this->formatOffer($offer, $property);
        });

        return response()->json([
            'success' => true,
            'data' => $transformed
        ]);
    }

    /**
     * Display a single offer by slug.
     */
    public function show($slug): JsonResponse
    {
        $offer = Offer::where('slug', $slug)->first();

        if (!$offer) {
            return response()->json(['success' => false, 'message' => 'Offer not found'], 404);
        }

        $property = !empty($offer->hotel) ? Property::with('parent')->find($offer->hotel) : null;
        $data = $this->formatOffer($offer, $property);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Format an offer into a complete, consistent API response array.
     */
    private function formatOffer(Offer $offer, ?Property $property = null): array
    {
        $data = $offer->toArray();

        // 1. Banner image
        if (!empty($data['banner_image'])) {
            $data['banner_image'] = $this->formatImageUrl($data['banner_image']);
        } else {
            $mediaUrl = $offer->getFirstMediaUrl('banner_image');
            if ($mediaUrl) {
                $data['banner_image'] = $mediaUrl;
            }
        }

        // 2. Gallery images
        if (!empty($data['images']) && is_array($data['images'])) {
            $data['images'] = array_values(array_filter(array_map(function ($img) {
                return is_string($img) ? $this->formatImageUrl($img) : null;
            }, $data['images'])));
        }

        // 3. Related Property info
        if (!$property && !empty($offer->hotel)) {
            $property = Property::with('parent')->find($offer->hotel);
        }

        $data['hotel_slug'] = $property ? $property->slug : 'offers';
        $data['hotel_name'] = $property ? (is_array($property->name) ? ($property->name['en'] ?? '') : $property->name) : null;
        $data['hotel_id'] = $property ? $property->id : null;

        // 4. Brand logo (from parent brand or property)
        $brandLogo = null;
        if ($property) {
            if ($property->parent && !empty($property->parent->logo)) {
                $brandLogo = $this->formatImageUrl($property->parent->logo);
            } elseif (!empty($property->logo)) {
                $brandLogo = $this->formatImageUrl($property->logo);
            }
        }
        $data['brand_logo'] = $brandLogo;

        // 5. Resolve any embedded images in translatable HTML fields
        foreach (['description', 'details_content', 'terms_conditions'] as $field) {
            if (isset($data[$field])) {
                if (is_array($data[$field])) {
                    foreach ($data[$field] as $loc => $val) {
                        if (is_string($val)) {
                            $data[$field][$loc] = $this->resolveHtmlImages($val);
                        }
                    }
                } elseif (is_string($data[$field])) {
                    $data[$field] = $this->resolveHtmlImages($data[$field]);
                }
            }
        }

        return $data;
    }

    /**
     * Format an image filename or relative path into a full absolute URL.
     */
    private function formatImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return $path;
        }

        $path = trim($path);

        // Already absolute URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // Protocol-relative URL
        if (str_starts_with($path, '//')) {
            return 'https:' . $path;
        }

        // Storage path
        if (str_starts_with($path, 'storage/') || str_starts_with($path, '/storage/')) {
            $clean = preg_replace('#^/?storage/#', '', $path);
            return url('storage/' . ltrim($clean, '/'));
        }

        // Uploads path or filename
        $clean = preg_replace('#^/?uploads/#', '', $path);
        return url('uploads/' . ltrim($clean, '/'));
    }

    /**
     * Replace src attributes in HTML content with full image URLs.
     */
    private function resolveHtmlImages(?string $html): ?string
    {
        if (empty($html) || !str_contains($html, '<img')) {
            return $html;
        }

        return preg_replace_callback('/(<img\b[^>]*?\bsrc=["\'])([^"\']+)(["\'][^>]*>)/i', function ($matches) {
            $prefix = $matches[1];
            $src = $matches[2];
            $suffix = $matches[3];

            return $prefix . ($this->formatImageUrl($src) ?? $src) . $suffix;
        }, $html);
    }
}
