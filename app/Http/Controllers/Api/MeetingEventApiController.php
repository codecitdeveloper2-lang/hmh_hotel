<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeetingEventPage;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingEventApiController extends Controller
{
    /**
     * Get meetings & events for a specific property by hotel slug.
     * GET /api/properties/{hotelSlug}/meetings-events
     */
    public function getByProperty(Request $request, string $hotelSlug): JsonResponse
    {
        $locale = $this->localeFromRequest($request);

        $cleanSlug = ltrim(rtrim($hotelSlug, '/'), '/');

        // Find properties matching this slug directly or via brand relationship
        $properties = Property::where('slug', $cleanSlug)
            ->orWhereHas('brand', function ($q) use ($cleanSlug) {
                $q->where('slug', $cleanSlug);
            })
            ->get();

        if ($properties->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        $property = $properties->firstWhere('slug', $cleanSlug) ?? $properties->first();
        $propertyIds = $properties->pluck('id')->toArray();

        // Fetch all active meeting & event pages for this property or its child hotels
        $eventPages = MeetingEventPage::with(['property.brand', 'seoMetadata'])
            ->whereIn('property_id', $propertyIds)
            ->where('is_active', true)
            ->get();





        // Main overview page config if present
        $mainPage = $eventPages->firstWhere('type', 'main_page');

        // Individual venue / event spaces list
        $eventSpaces = $eventPages->where('type', '!=', 'main_page')->map(function ($page) use ($locale) {
            return $this->formatEventPage($page, $locale);
        })->values();

        $formattedMainPage = $mainPage ? $this->formatEventPage($mainPage, $locale) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'property' => [
                    'id' => $property->id,
                    'name' => is_array($property->name) ? ($property->name['en'] ?? '') : $property->name,
                    'slug' => $property->slug,
                    'logo' => $property->getFirstMediaUrl('logo') ?: ($property->logo ? url('uploads/' . ltrim($property->logo, '/')) : null),
                ],
                'main_page' => $formattedMainPage,
                'event_spaces' => $eventSpaces,
            ]
        ]);
    }

    /**
     * Get all active meetings & event spaces across properties (with optional filters).
     * GET /api/meetings-events
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $this->localeFromRequest($request);

        $query = MeetingEventPage::with(['property.brand', 'seoMetadata'])
            ->where('is_active', true);

        if ($request->has('hotel_slug')) {
            $hotel = Property::where('slug', $request->query('hotel_slug'))->first();
            if ($hotel) {
                $query->where('property_id', $hotel->id);
            }
        } elseif ($request->has('property_id')) {
            $query->where('property_id', $request->query('property_id'));
        }

        if ($request->has('type')) {
            $query->where('type', $request->query('type'));
        }

        $pages = $query->get();

        $data = $pages->map(function ($page) use ($locale) {
            return $this->formatEventPage($page, $locale);
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get a single meeting & event space by slug or ID.
     * GET /api/meetings-events/{slug}
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $locale = $this->localeFromRequest($request);

        $page = MeetingEventPage::with(['property.brand', 'seoMetadata'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$page && is_numeric($slug)) {
            $page = MeetingEventPage::with(['property.brand', 'seoMetadata'])
                ->where('id', $slug)
                ->where('is_active', true)
                ->first();
        }

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Meeting & Event page not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatEventPage($page, $locale)
        ]);
    }

    /**
     * Get a single meeting & event space for a specific property by hotel slug and event slug/ID.
     * GET /api/properties/{hotelSlug}/meetings-events/{eventSlug}
     */
    public function showByProperty(Request $request, string $hotelSlug, string $eventSlug): JsonResponse
    {
        $locale = $this->localeFromRequest($request);

        $cleanSlug = ltrim(rtrim($hotelSlug, '/'), '/');

        $properties = Property::where('slug', $cleanSlug)
            ->orWhereHas('brand', function ($q) use ($cleanSlug) {
                $q->where('slug', $cleanSlug);
            })
            ->get();

        if ($properties->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found'
            ], 404);
        }

        $propertyIds = $properties->pluck('id')->toArray();

        // If hotel is specified via query parameter, narrow down propertyIds
        if ($request->has('hotel')) {
            $specificHotel = Property::where('slug', $request->query('hotel'))->first();
            if ($specificHotel) {
                $propertyIds = [$specificHotel->id];
            }
        }

        $page = MeetingEventPage::with(['property', 'seoMetadata'])
            ->whereIn('property_id', $propertyIds)
            ->where('slug', $eventSlug)
            ->where('is_active', true)
            ->first();

        if (!$page && is_numeric($eventSlug)) {
            $page = MeetingEventPage::with(['property', 'seoMetadata'])
                ->whereIn('property_id', $propertyIds)
                ->where('id', $eventSlug)
                ->where('is_active', true)
                ->first();
        }

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Meeting & Event space not found for this property'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatEventPage($page, $locale)
        ]);
    }

    private function formatEventPage(MeetingEventPage $page, string $locale): array
    {
        $property = $page->property;
        $hotelName = $property ? (is_array($property->name) ? ($property->name['en'] ?? '') : $property->name) : null;
        $brand = $property?->brand;
        $brandName = $brand ? (is_array($brand->name) ? ($brand->name['en'] ?? '') : $brand->name) : ($hotelName ?: 'HMH Hotel Group');
        $brandSlug = $brand ? $brand->slug : null;

        $contactDetails = $this->formatJsonField($page->contact_details);
        if (is_array($contactDetails)) {
            if (empty($contactDetails['phone']) && !empty($property?->phone)) {
                $contactDetails['phone'] = $property->phone;
            }
            if (empty($contactDetails['email']) && !empty($property?->email)) {
                $contactDetails['email'] = $property->email;
            }
        }

        $rfpUrl = $page->rfp_url;
        if (empty($rfpUrl) && !empty($property?->rfp_url)) {
            $rfpUrl = $property->rfp_url;
        }

        $coverImage = $page->image;
        if (empty($coverImage) && !empty($page->gallery)) {
            $galleryItems = $this->formatJsonField($page->gallery);
            if (!empty($galleryItems) && is_array($galleryItems)) {
                $first = reset($galleryItems);
                $coverImage = is_array($first) ? ($first['image'] ?? $first['src'] ?? null) : $first;
            }
        }

        return [
            'id' => $page->id,
            'property_id' => $page->property_id,
            'hotel_name' => $hotelName,
            'hotel_slug' => $property?->slug,
            'brand_name' => $brandName,
            'brand_slug' => $brandSlug,
            'type' => $page->type,
            'slug' => $page->slug,
            'title' => $this->parseTranslation($page->title, $locale),
            'subtitle' => $this->parseTranslation($page->subtitle, $locale),
            'description' => $this->parseTranslation($page->description, $locale),
            'details_content' => $this->parseTranslation($page->details_content, $locale),
            'capacity_details' => $this->parseTranslation($page->capacity_details, $locale),
            'area_sqft' => $page->area_sqft,
            'area_sqm' => $page->area_sqm,
            'ceiling_height' => $page->ceiling_height,
            'highlights' => $this->formatJsonField($page->highlights),
            'capacities' => $this->formatJsonField($page->capacities),
            'capacity_table' => $this->formatJsonField($page->capacity_table),
            'banner_slides' => $this->formatSlidesOrCards($page->banner_slides),
            'event_cards' => $this->formatSlidesOrCards($page->event_cards),
            'gallery' => $this->formatGallery($page->gallery, $coverImage),
            'image' => $this->formatImageUrl($coverImage),
            'rfp_url' => $rfpUrl,
            'contact_details' => $contactDetails,
            'is_active' => (bool) $page->is_active,
            'seo' => [
                'meta_title' => $page->seoMetadata?->meta_title,
                'meta_description' => $page->seoMetadata?->meta_description,
            ],
            'created_at' => $page->created_at,
            'updated_at' => $page->updated_at,
        ];
    }

    private function parseTranslation($value, string $locale = 'en')
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            }
        }

        if (is_array($value)) {
            return $value[$locale] ?? $value['en'] ?? reset($value) ?: null;
        }

        return !empty($value) ? $value : null;
    }

    private function formatJsonField($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
        }
        return $value ?? [];
    }

    private function formatImageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return url('uploads/' . ltrim($path, '/'));
    }

    private function formatGallery($gallery, ?string $fallbackImage = null): array
    {
        $items = $this->formatJsonField($gallery);
        if (!is_array($items) || empty($items)) {
            if (!empty($fallbackImage)) {
                $formatted = $this->formatImageUrl($fallbackImage);
                return $formatted ? [$formatted] : [];
            }
            return [];
        }

        return array_values(array_map(function ($item) {
            if (is_string($item)) {
                return $this->formatImageUrl($item);
            }
            if (is_array($item)) {
                if (isset($item['image'])) {
                    $item['image'] = $this->formatImageUrl($item['image']);
                }
                if (isset($item['src'])) {
                    $item['src'] = $this->formatImageUrl($item['src']);
                }
                return $item;
            }
            return $item;
        }, $items));
    }

    private function formatSlidesOrCards($data): array
    {
        $items = $this->formatJsonField($data);
        if (!is_array($items)) {
            return [];
        }

        return array_values(array_map(function ($item) {
            if (is_array($item)) {
                if (isset($item['image'])) {
                    $item['image'] = $this->formatImageUrl($item['image']);
                }
                if (isset($item['background_image'])) {
                    $item['background_image'] = $this->formatImageUrl($item['background_image']);
                }
            }
            return $item;
        }, $items));
    }

    private function localeFromRequest(Request $request): string
    {
        $locale = $request->header('X-Locale')
            ?? $request->header('X-Local')
            ?? $request->query('lang')
            ?? config('app.locale', 'en');

        return in_array($locale, ['en', 'ar'], true) ? $locale : 'en';
    }
}
