<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;

class PropertyApiController extends Controller
{
    /**
     * Get a property by its slug.
     */
    public function show(string $slug): JsonResponse
    {
        $property = Property::with([
            'roomTypes',
            'diningOutlets',
            'amenities',
            'attractions',
            'offers',
            'brand',  // load parent brand so we can inherit its logo
            'children' => function($query) {
                // eager-load child hotels (for brand pages)
                $query->where('type', 'hotel')
                      ->where('is_active', true)
                      ->orderBy('sort_order', 'asc');
            },
            'children.amenities',
            'children.roomTypes',
            'children.diningOutlets',
            'children.offers',
        ])->where('slug', $slug)->first();

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return response()->json([
            'data' => $this->formatProperty($property)
        ]);
    }

    /**
     * Get all active brands and their active hotels for the global header.
     */
        /**
     * Get all active hotels.
     */
    public function getAllHotels(): JsonResponse
    {
        $hotels = Property::where('type', 'hotel')
            ->where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($hotel) {
                $name = is_array($hotel->name) ? ($hotel->name['en'] ?? '') : $hotel->name;
                return [
                    'id' => $hotel->id,
                    'name' => $name,
                    'slug' => $hotel->slug,
                    'brand_id' => $hotel->parent_id,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $hotels
        ]);
    }

    public function getBrandsAndHotels(): JsonResponse
    {
        $brands = Property::where('type', 'brand')
            ->where('is_active', true)
            ->with(['hotels' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('sort_order', 'asc');
            }])
            ->orderBy('sort_order', 'asc')
            ->get();

        $data = $brands->map(function ($brand) {
            $name = is_array($brand->name) ? ($brand->name['en'] ?? '') : $brand->name;
            $logo = $brand->getFirstMediaUrl('logo') ?: ($brand->logo ? url('uploads/' . ltrim($brand->logo, '/')) : null);
            $brandContent = is_array($brand->brand_content) ? $brand->brand_content : [];
            $footerLogoRaw = $brandContent['footer_brand_logo'] ?? null;
            $footerLogo = $footerLogoRaw ? (str_starts_with($footerLogoRaw, 'http') ? $footerLogoRaw : url('uploads/' . ltrim($footerLogoRaw, '/'))) : null;

            return [
                'id' => $brand->id,
                'name' => $name,
                'slug' => $brand->slug,
                'logo' => $logo,
                'footer_logo' => $footerLogo,
                'hotels' => $brand->hotels->map(function ($hotel) {
                    $hName = is_array($hotel->name) ? ($hotel->name['en'] ?? '') : $hotel->name;
                    $hBrandContent = is_array($hotel->brand_content) ? $hotel->brand_content : [];
                    $hFooterLogoRaw = $hBrandContent['footer_brand_logo'] ?? null;
                    $hFooterLogo = $hFooterLogoRaw ? (str_starts_with($hFooterLogoRaw, 'http') ? $hFooterLogoRaw : url('uploads/' . ltrim($hFooterLogoRaw, '/'))) : null;
                    return [
                        'id' => $hotel->id,
                        'name' => $hName,
                        'slug' => $hotel->slug,
                        'footer_logo' => $hFooterLogo,
                    ];
                })->toArray(),
            ];
        });

        return response()->json(['data' => $data]);
    }

    private function getBrandOffers(Property $property): array
    {
        // Offers from pivot table
        $offers = $property->offers;

        // Offers assigned via the 'hotel' column directly to this property
        $directOffers = \App\Models\Offer::where('hotel', $property->id)->where('is_active', true)->get();
        $offers = $offers->merge($directOffers);

        if ($property->type === 'brand') {
            $childIds = $property->children->pluck('id')->toArray();
            $childDirectOffers = \App\Models\Offer::whereIn('hotel', $childIds)->where('is_active', true)->get();
            $offers = $offers->merge($childDirectOffers);

            foreach ($property->children as $child) {
                $offers = $offers->merge($child->offers); // Pivot offers from children
            }
        }

        return $offers->unique('id')->map(function ($offer) {
            $bannerImage = $offer->banner_image;
            if (!empty($bannerImage) && !str_starts_with($bannerImage, 'http')) {
                $bannerImage = url('/uploads/' . ltrim($bannerImage, '/'));
            }

            $hotelProperty = $offer->hotel ? \App\Models\Property::find($offer->hotel) : null;
            $hotelSlug = $hotelProperty ? $hotelProperty->slug : 'offers';

            return [
                'id' => $offer->id,
                'slug' => $offer->slug,
                'hotel_slug' => $hotelSlug,
                'name' => is_array($offer->name) ? ($offer->name['en'] ?? '') : $offer->name,
                'description' => is_array($offer->description) ? ($offer->description['en'] ?? '') : $offer->description,
                'discount_percentage' => $offer->discount_percentage,
                'badge' => $offer->badge ?? 'OFFER',
                'image' => $offer->getFirstMediaUrl('featured_image') ?: $bannerImage,
            ];
        })->values()->toArray();
    }

    /**
     * Format the property data to exactly what the frontend needs.
     */
    private function formatProperty(Property $property): array
    {
        $name = is_array($property->name) ? ($property->name['en'] ?? '') : $property->name;
        $description = is_array($property->description) ? ($property->description['en'] ?? '') : $property->description;

        // Resolve own logo first; if absent and this is a hotel, fall back to the parent brand's logo
        $ownLogo = $property->getFirstMediaUrl('logo') ?: ($property->logo ? url('uploads/' . ltrim($property->logo, '/')) : null);
        if (!$ownLogo && $property->type === 'hotel' && $property->brand) {
            $brand = $property->brand;
            $ownLogo = $brand->getFirstMediaUrl('logo') ?: ($brand->logo ? url('uploads/' . ltrim($brand->logo, '/')) : null);
        }

        // Resolve brand_content (section config stored as JSON)
        $brandContent = is_array($property->brand_content) ? $property->brand_content : [];

        // Resolve footer_logo (from own brand_content or parent brand's brand_content if this is a hotel)
        $footerLogoRaw = $brandContent['footer_brand_logo'] ?? null;
        if (!$footerLogoRaw && $property->type === 'hotel' && $property->brand) {
            $parentBrandContent = is_array($property->brand->brand_content) ? $property->brand->brand_content : [];
            $footerLogoRaw = $parentBrandContent['footer_brand_logo'] ?? null;
        }
        $footerLogo = null;
        if ($footerLogoRaw) {
            $footerLogo = str_starts_with($footerLogoRaw, 'http') ? $footerLogoRaw : url('uploads/' . ltrim($footerLogoRaw, '/'));
        }

        return [
            'id' => $property->id,
            'name' => $name,
            'slug' => $property->slug,
            'type' => $property->type,
            'parent_id' => $property->parent_id,
            'brand_slug' => $property->brand?->slug,
            'tagline' => $property->tagline,
            'star_segment' => $property->star_segment,
            'intro_title' => $this->parseTranslation($property->intro_title),
            'intro_subtitle' => $this->parseTranslation($property->intro_subtitle),
            'intro_text' => $this->parseTranslation($property->intro_text),
            'description' => $description,
            'logo' => $ownLogo,
            'footer_logo' => $footerLogo,
            'cover_image' => $property->getFirstMediaUrl('cover_image') ?: null,
            'banner_images' => $this->resolveBannerImages($property),
            'banner_title' => $property->banner_title,
            'star_rating' => $property->star_rating,

            // Brand-page location / contact fields
            'latitude'            => $property->latitude ? (float) $property->latitude : null,
            'longitude'           => $property->longitude ? (float) $property->longitude : null,
            'address'             => $property->address,
            'city'                => $property->city,
            'country'             => $property->country,
            'phone'               => $property->phone,
            'email'               => $property->email,
            'google_location'     => $property->google_location,
            'location_title'      => $property->location_title,
            'contact_button_text' => $property->contact_button_text,
            'contact_button_url'  => $property->contact_button_url,

            // Brand content section config
            'brand_content' => [
                'experience_section_title' => $brandContent['experience_section_title'] ?? null,
                'our_hotels_section_title' => $brandContent['our_hotels_section_title'] ?? null,
                'our_hotels_cta_link'      => $brandContent['our_hotels_cta_link'] ?? null,
                'destinations_section_title' => $brandContent['destinations_section_title'] ?? null,
                'destinations_cta_link'     => $brandContent['destinations_cta_link'] ?? null,
            ],

            // Child hotels (only populated when type === 'brand')
            'child_hotels' => $property->children->map(function ($hotel) {
                $hotelName = is_array($hotel->name) ? ($hotel->name['en'] ?? '') : $hotel->name;
                $hotelLogo = $hotel->getFirstMediaUrl('logo')
                    ?: ($hotel->logo ? url('uploads/' . ltrim($hotel->logo, '/')) : null);

                // Collect banner images for the hotel card
                $bannerImages = $this->resolveBannerImages($hotel);
                $coverImage   = $hotel->getFirstMediaUrl('cover_image')
                    ?: ($hotel->cover_image ? url('uploads/' . ltrim($hotel->cover_image, '/')) : null)
                    ?: ($bannerImages[0] ?? null);

                return [
                    'id'                   => $hotel->id,
                    'name'                 => $hotelName,
                    'slug'                 => $hotel->slug,
                    'logo'                 => $hotelLogo,
                    'cover_image'          => $coverImage,
                    'banner_images'        => $bannerImages,
                    'address'              => $hotel->address,
                    'city'                 => $hotel->city,
                    'country'              => $hotel->country,
                    'latitude'             => $hotel->latitude ? (float) $hotel->latitude : null,
                    'longitude'            => $hotel->longitude ? (float) $hotel->longitude : null,
                    'phone'                => $hotel->phone,
                    'email'                => $hotel->email,
                    'travelclick_hotel_id' => $hotel->travelclick_hotel_id,
                    'website'              => $hotel->website,
                    'google_location'      => $hotel->google_location,
                    'star_rating'          => $hotel->star_rating,
                    'sort_order'           => $hotel->sort_order,
                    'room_types'           => $hotel->roomTypes->map(fn($room) => $this->formatRoomType($room, $hotel))->values()->toArray(),
                ];
            })->toArray(),

            // Format Relations
            'room_types' => $this->resolveRoomTypes($property),

            'dining_outlets' => $this->resolveDiningOutlets($property),

            'amenities' => $property->amenities->map(function ($amenity) {
                $amenitiesList = is_string($amenity->amenities_list)
                    ? json_decode($amenity->amenities_list, true)
                    : ($amenity->amenities_list ?? []);

                $formattedList = collect($amenitiesList ?? [])->map(function ($item) use ($amenity) {
                    $icon = $item['icon'] ?? null;
                    if ($icon && !str_starts_with($icon, 'http://') && !str_starts_with($icon, 'https://')) {
                        $icon = url('uploads/' . ltrim($icon, '/'));
                    }
                    return [
                        'name' => $item['name'] ?? '',
                        'description' => $item['description'] ?? '',
                        'icon' => $icon,
                        'image' => $icon,
                        'button_label' => $item['button_label'] ?? 'Call Us',
                        'button_link' => $item['button_link'] ?? ($amenity->call_us_no ? 'tel:' . preg_replace('/[^\d+]/', '', $amenity->call_us_no) : null),
                    ];
                })->values()->toArray();

                $gallery = collect(is_string($amenity->gallery) ? json_decode($amenity->gallery, true) : ($amenity->gallery ?? []))->map(function ($img) {
                    if ($img && !str_starts_with($img, 'http://') && !str_starts_with($img, 'https://')) {
                        return url('uploads/' . ltrim($img, '/'));
                    }
                    return $img;
                })->filter()->values()->toArray();

                return [
                    'id' => $amenity->id,
                    'title' => is_array($amenity->title) ? ($amenity->title['en'] ?? '') : $amenity->title,
                    'subtitle' => is_array($amenity->subtitle) ? ($amenity->subtitle['en'] ?? '') : ($amenity->subtitle ?: 'Facilities'),
                    'description' => $amenity->description,
                    'category' => $amenity->category,
                    'read_more_label' => $amenity->read_more_label,
                    'read_more_link' => $amenity->read_more_link,
                    'call_us_no' => $amenity->call_us_no,
                    'amenities_list' => $formattedList,
                    'gallery' => $gallery,
                    'image' => $amenity->getFirstMediaUrl('featured_image') ?: ($gallery[0] ?? null),
                ];
            })->toArray(),

            'attractions' => $property->attractions->map(function ($attr) {
                $featuredImage = $attr->getFirstMediaUrl('featured_image') ?: null;
                $gallery = $attr->getMedia('attraction_gallery')->map(fn($m) => $m->getUrl())->toArray();
                if (empty($gallery) && $featuredImage) {
                    $gallery = [$featuredImage];
                }

                return [
                    'id' => $attr->id,
                    'name' => is_array($attr->name) ? ($attr->name['en'] ?? '') : $attr->name,
                    'slug' => $attr->slug,
                    'category' => $attr->category,
                    'distance_from_hotel' => $attr->distance_from_hotel,
                    'description' => is_array($attr->description) ? ($attr->description['en'] ?? '') : $attr->description,
                    'address' => $attr->address,
                    'latitude' => $attr->latitude ? (float) $attr->latitude : null,
                    'longitude' => $attr->longitude ? (float) $attr->longitude : null,
                    'google_maps_url' => $attr->google_maps_url,
                    'image' => $featuredImage,
                    'gallery' => $gallery,
                ];
            })->toArray(),

            'offers' => $this->getBrandOffers($property),
        ];
    }

    private function resolveDiningOutlets(Property $property): array
    {
        if ($property->type === 'brand') {
            $allDining = collect();
            foreach ($property->children as $child) {
                foreach ($child->diningOutlets()->where('is_active', true)->orderBy('sort_order')->get() as $dining) {
                    $allDining->push($this->formatDiningOutlet($dining, $child));
                }
            }
            return $allDining->values()->toArray();
        }

        return $property->diningOutlets()->where('is_active', true)->orderBy('sort_order')->get()->map(fn($dining) => $this->formatDiningOutlet($dining, $property))->values()->toArray();
    }

    private function formatDiningOutlet($dining, $hotel = null): array
    {
        $gallery = $dining->getMedia('dining_gallery')->map(fn($m) => $m->getUrl())->toArray();
        if (empty($gallery) && $dining->getFirstMediaUrl('featured_image')) {
            $gallery = [$dining->getFirstMediaUrl('featured_image')];
        }

        $hotelName = $hotel ? (is_array($hotel->name) ? ($hotel->name['en'] ?? '') : $hotel->name) : null;

        return [
            'id' => $dining->id,
            'hotel_id' => $hotel?->id ?? $dining->property_id,
            'hotel_name' => $hotelName,
            'hotel_slug' => $hotel?->slug,
            'name' => is_array($dining->name) ? ($dining->name['en'] ?? '') : $dining->name,
            'slug' => $dining->slug,
            'description' => is_array($dining->description) ? ($dining->description['en'] ?? '') : $dining->description,
            'cuisine_type' => is_array($dining->cuisine_type) ? ($dining->cuisine_type['en'] ?? '') : $dining->cuisine_type,
            'opening_hours' => is_array($dining->opening_hours) ? ($dining->opening_hours['en'] ?? '') : $dining->opening_hours,
            'image' => $dining->getFirstMediaUrl('featured_image') ?: null,
            'gallery' => $gallery,
            'read_more_label' => $dining->read_more_label ?: 'READ MORE',
            'read_more_link' => $dining->read_more_link 
                ? preg_replace('#^/(coral-dubai-deira-hotel|coral-hotels-resorts-dubai-deira|opera-grand-hotel)/dining/#', '/dining/', $dining->read_more_link) 
                : "/dining/{$dining->slug}",
            'contact_details' => $dining->contact_details,
            'book_table_label' => $dining->book_table_label ?: 'BOOK A TABLE',
            'book_table_link' => $dining->book_table_link,
            'has_table_booking' => (bool)$dining->has_table_booking,
        ];
    }

    private function resolveRoomTypes(Property $property): array
    {
        if ($property->type === 'brand') {
            $allRooms = collect();
            foreach ($property->children as $child) {
                foreach ($child->roomTypes as $room) {
                    $allRooms->push($this->formatRoomType($room, $child));
                }
            }
            return $allRooms->values()->toArray();
        }

        return $property->roomTypes->map(fn($room) => $this->formatRoomType($room, $property))->values()->toArray();
    }

    private function formatRoomType($room, $hotel = null): array
    {
        $roomName = is_array($room->name) ? ($room->name['en'] ?? '') : $room->name;
        $description = is_array($room->description) ? ($room->description['en'] ?? '') : $room->description;
        $shortDescription = is_array($room->short_description) ? ($room->short_description['en'] ?? '') : $room->short_description;

        $gallery = array_values(array_unique(array_filter(array_merge(
            $room->getMedia('featured_image')->map(fn($m) => $m->getUrl())->toArray(),
            $room->getMedia('additional_gallery')->map(fn($m) => $m->getUrl())->toArray(),
            $room->getMedia('gallery')->map(fn($m) => $m->getUrl())->toArray()
        ))));

        $image = $room->getFirstMediaUrl('featured_image')
            ?: ($room->getFirstMediaUrl('gallery')
            ?: ($room->getFirstMediaUrl('additional_gallery')
            ?: ($gallery[0] ?? null)));

        $hotelName = $hotel ? (is_array($hotel->name) ? ($hotel->name['en'] ?? '') : $hotel->name) : null;

        return [
            'id' => $room->id,
            'hotel_id' => $hotel?->id ?? $room->property_id,
            'hotel_name' => $hotelName,
            'hotel_slug' => $hotel?->slug,
            'hotel_city' => $hotel?->city,
            'hotel_country' => $hotel?->country,
            'name' => $roomName,
            'slug' => $room->slug,
            'description' => $description,
            'short_description' => $shortDescription,
            'size_sqm' => $room->size_sqm,
            'bed_type' => $room->bed_type,
            'image' => $image,
            'gallery' => $gallery,
            'starting_price' => $room->starting_price,
            'read_more_label' => $room->read_more_label ?: 'DISCOVER MORE',
            'read_more_link' => $room->read_more_link ? preg_replace('#^/(coral-dubai-deira-hotel|coral-hotels-resorts-dubai-deira|opera-grand-hotel)/rooms-suites/#', '/rooms-suites/', $room->read_more_link) : "/rooms-suites/{$room->slug}",
            'book_now_label' => $room->book_now_label ?: 'BOOK NOW',
            'book_now_link' => $room->book_now_link,
            'special_features' => $room->special_features,
        ];
    }

    private function getMediaUrls($model, $collection) {
        $urls = [];
        foreach ($model->getMedia($collection) as $media) {
            $urls[] = $media->getUrl();
        }
        return $urls;
    }

    /**
     * Safely parse a translation field that might be a JSON string or array,
     * returning the English text or null if empty.
     */
    private function parseTranslation($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $value = $decoded;
            }
        }

        if (is_array($value)) {
            $text = $value['en'] ?? null;
            return !empty($text) ? $text : null;
        }

        return !empty($value) ? $value : null;
    }

    /**
     * Resolve banner images to full URLs.
     * Priority: Spatie hero_images media > raw banner_images column (filenames resolved via uploads disk).
     */
    private function resolveBannerImages(Property $property): array
    {
        // 1. Try Spatie Media Library 'hero_images' collection first
        $mediaUrls = $this->getMediaUrls($property, 'hero_images');
        if (!empty($mediaUrls)) {
            return $mediaUrls;
        }

        // 2. Fall back to the raw banner_images column
        $raw = $property->banner_images;
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (!is_array($raw)) {
            return [];
        }

        // Resolve plain filenames to full URLs using the uploads disk
        return array_values(array_filter(array_map(function ($img) {
            if (empty($img)) return null;
            // Already a full URL
            if (str_starts_with($img, 'http')) return $img;
            // Build URL using the uploads disk base URL
            return url('uploads/' . ltrim($img, '/'));
        }, $raw)));
    }

    /**
     * Get single dining outlet details with hotel context and sibling outlets.
     */
    public function getDiningDetails(string $hotelSlug, string $diningSlug)
    {
        $property = Property::where('slug', $hotelSlug)->first();
        if (!$property) {
            return response()->json(['error' => 'Hotel not found'], 404);
        }

        $propertyIds = [$property->id];
        if ($property->type === 'brand') {
            $propertyIds = array_merge($propertyIds, $property->children->pluck('id')->toArray());
        }

        $cleanSlug = preg_replace('/^(deira-|opera-)/', '', $diningSlug);
        $dining = \App\Models\DiningOutlet::whereIn('property_id', $propertyIds)
            ->where(function ($q) use ($diningSlug, $cleanSlug) {
                $q->where('slug', $diningSlug)
                  ->orWhere('slug', $cleanSlug)
                  ->orWhere('slug', 'deira-' . $cleanSlug);
                if (is_numeric($diningSlug)) {
                    $q->orWhere('id', (int)$diningSlug);
                }
            })
            ->first();

        if (!$dining) {
            return response()->json(['error' => 'Dining outlet not found'], 404);
        }

        $outletProperty = $dining->property_id === $property->id ? $property : (Property::find($dining->property_id) ?? $property);
        $allOutlets = $outletProperty->diningOutlets()->where('is_active', true)->orderBy('sort_order')->get();
        $gallery = $dining->getMedia('dining_gallery')->map(fn($m) => $m->getUrl())->toArray();
        if (empty($gallery) && $dining->getFirstMediaUrl('featured_image')) {
            $gallery = [$dining->getFirstMediaUrl('featured_image')];
        }

        $formattedDining = [
            'id' => $dining->id,
            'hotel_id' => $outletProperty->id,
            'hotel_name' => is_array($outletProperty->name) ? ($outletProperty->name['en'] ?? '') : $outletProperty->name,
            'hotel_slug' => $outletProperty->slug,
            'name' => is_array($dining->name) ? ($dining->name['en'] ?? '') : $dining->name,
            'slug' => $dining->slug,
            'description' => is_array($dining->description) ? ($dining->description['en'] ?? '') : $dining->description,
            'cuisine_type' => is_array($dining->cuisine_type) ? ($dining->cuisine_type['en'] ?? '') : $dining->cuisine_type,
            'opening_hours' => is_array($dining->opening_hours) ? ($dining->opening_hours['en'] ?? '') : $dining->opening_hours,
            'image' => $dining->getFirstMediaUrl('featured_image') ?: null,
            'gallery' => $gallery,
            'read_more_label' => $dining->read_more_label ?: 'READ MORE',
            'read_more_link' => $dining->read_more_link 
                ? preg_replace('#^/(coral-dubai-deira-hotel|coral-hotels-resorts-dubai-deira|opera-grand-hotel)/dining/#', '/dining/', $dining->read_more_link) 
                : "/dining/{$dining->slug}",
            'contact_details' => $dining->contact_details,
            'book_table_label' => $dining->book_table_label ?: 'BOOK A TABLE',
            'book_table_link' => $dining->book_table_link,
            'has_table_booking' => (bool)$dining->has_table_booking,
        ];

        return response()->json([
            'hotel' => [
                'id' => $outletProperty->id,
                'name' => is_array($outletProperty->name) ? ($outletProperty->name['en'] ?? '') : $outletProperty->name,
                'slug' => $outletProperty->slug,
                'logo' => $outletProperty->getFirstMediaUrl('logo') ?: ($outletProperty->logo ? url('uploads/' . ltrim($outletProperty->logo, '/')) : null),
                'phone' => $outletProperty->phone,
                'email' => $outletProperty->email,
            ],
            'dining' => $formattedDining,
            'other_outlets' => $allOutlets->where('id', '!=', $dining->id)->map(fn($d) => [
                'id' => $d->id,
                'name' => is_array($d->name) ? ($d->name['en'] ?? '') : $d->name,
                'slug' => $d->slug,
                'cuisine_type' => is_array($d->cuisine_type) ? ($d->cuisine_type['en'] ?? '') : $d->cuisine_type,
                'image' => $d->getFirstMediaUrl('featured_image') ?: null,
            ])->values()->toArray(),
        ]);
    }

    /**
     * Get single attraction details with hotel context and sibling attractions for navigation.
     */
    public function getAttractionDetails(string $hotelSlug, string $attractionSlug)
    {
        $property = Property::where('slug', $hotelSlug)->first();
        if (!$property) {
            return response()->json(['error' => 'Hotel not found'], 404);
        }

        $propertyIds = [$property->id];
        if ($property->type === 'brand') {
            $propertyIds = array_merge($propertyIds, $property->children->pluck('id')->toArray());
        }

        $attraction = \App\Models\Attraction::whereIn('property_id', $propertyIds)
            ->where(function ($q) use ($attractionSlug) {
                $q->where('slug', $attractionSlug);
                if (is_numeric($attractionSlug)) {
                    $q->orWhere('id', (int)$attractionSlug);
                }
            })
            ->first();

        if (!$attraction) {
            return response()->json(['error' => 'Attraction not found'], 404);
        }

        $attrProperty = $attraction->property_id === $property->id ? $property : (Property::find($attraction->property_id) ?? $property);
        $allAttractions = $attrProperty->attractions()->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();

        $featuredImage = $attraction->getFirstMediaUrl('featured_image') ?: null;
        $gallery = $attraction->getMedia('attraction_gallery')->map(fn($m) => $m->getUrl())->toArray();
        if (empty($gallery) && $featuredImage) {
            $gallery = [$featuredImage];
        }

        $formattedAttraction = [
            'id' => $attraction->id,
            'hotel_id' => $attrProperty->id,
            'hotel_name' => is_array($attrProperty->name) ? ($attrProperty->name['en'] ?? '') : $attrProperty->name,
            'hotel_slug' => $attrProperty->slug,
            'name' => is_array($attraction->name) ? ($attraction->name['en'] ?? '') : $attraction->name,
            'slug' => $attraction->slug,
            'category' => $attraction->category,
            'distance_from_hotel' => $attraction->distance_from_hotel,
            'description' => is_array($attraction->description) ? ($attraction->description['en'] ?? '') : $attraction->description,
            'address' => $attraction->address,
            'latitude' => $attraction->latitude ? (float) $attraction->latitude : null,
            'longitude' => $attraction->longitude ? (float) $attraction->longitude : null,
            'google_maps_url' => $attraction->google_maps_url,
            'image' => $featuredImage,
            'gallery' => $gallery,
            'read_more_label' => $attraction->read_more_label ?: 'READ MORE',
            'read_more_link' => $attraction->read_more_link,
        ];

        return response()->json([
            'hotel' => [
                'id' => $attrProperty->id,
                'name' => is_array($attrProperty->name) ? ($attrProperty->name['en'] ?? '') : $attrProperty->name,
                'slug' => $attrProperty->slug,
                'logo' => $attrProperty->getFirstMediaUrl('logo') ?: ($attrProperty->logo ? url('uploads/' . ltrim($attrProperty->logo, '/')) : null),
                'footer_logo' => $attrProperty->footer_logo ? url('uploads/' . ltrim($attrProperty->footer_logo, '/')) : null,
                'phone' => $attrProperty->phone,
                'email' => $attrProperty->email,
                'address' => $attrProperty->address,
            ],
            'attraction' => $formattedAttraction,
            'all_attractions' => $allAttractions->map(fn($a) => [
                'id' => $a->id,
                'name' => is_array($a->name) ? ($a->name['en'] ?? '') : $a->name,
                'slug' => $a->slug,
                'category' => $a->category,
                'distance_from_hotel' => $a->distance_from_hotel,
                'description' => is_array($a->description) ? ($a->description['en'] ?? '') : $a->description,
                'address' => $a->address,
                'latitude' => $a->latitude ? (float) $a->latitude : null,
                'longitude' => $a->longitude ? (float) $a->longitude : null,
                'image' => $a->getFirstMediaUrl('featured_image') ?: null,
            ])->values()->toArray(),
        ]);
    }
}
