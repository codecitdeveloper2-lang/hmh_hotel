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
            return [
                'id' => $brand->id,
                'name' => $name,
                'slug' => $brand->slug,
                'logo' => $logo,
                'hotels' => $brand->hotels->map(function ($hotel) {
                    $hName = is_array($hotel->name) ? ($hotel->name['en'] ?? '') : $hotel->name;
                    return [
                        'id' => $hotel->id,
                        'name' => $hName,
                        'slug' => $hotel->slug,
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

            return [
                'id' => $offer->id,
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
            'cover_image' => $property->getFirstMediaUrl('cover_image') ?: null,
            'banner_images' => $this->resolveBannerImages($property),
            'banner_title' => $property->banner_title,
            'star_rating' => $property->star_rating,

            // Brand-page location / contact fields
            'google_location' => $property->google_location,
            'location_title' => $property->location_title,
            'contact_button_text' => $property->contact_button_text,
            'contact_button_url' => $property->contact_button_url,

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
                    'phone'                => $hotel->phone,
                    'email'                => $hotel->email,
                    'travelclick_hotel_id' => $hotel->travelclick_hotel_id,
                    'website'              => $hotel->website,
                    'google_location'      => $hotel->google_location,
                    'star_rating'          => $hotel->star_rating,
                    'sort_order'           => $hotel->sort_order,
                ];
            })->toArray(),

            // Format Relations
            'room_types' => $property->roomTypes->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => is_array($room->name) ? ($room->name['en'] ?? '') : $room->name,
                    'description' => is_array($room->description) ? ($room->description['en'] ?? '') : $room->description,
                    'size_sqm' => $room->size_sqm,
                    'bed_type' => $room->bed_type,
                    'image' => $room->getFirstMediaUrl('gallery') ?: null,
                ];
            })->toArray(),

            'dining_outlets' => $property->diningOutlets->map(function ($dining) {
                return [
                    'id' => $dining->id,
                    'name' => is_array($dining->name) ? ($dining->name['en'] ?? '') : $dining->name,
                    'description' => is_array($dining->description) ? ($dining->description['en'] ?? '') : $dining->description,
                    'cuisine_type' => $dining->cuisine_type,
                    'image' => $dining->getFirstMediaUrl('featured_image') ?: null,
                ];
            })->toArray(),

            'amenities' => $property->amenities->map(function ($amenity) {
                return [
                    'id' => $amenity->id,
                    'title' => is_array($amenity->title) ? ($amenity->title['en'] ?? '') : $amenity->title,
                    'category' => $amenity->category,
                    'image' => $amenity->getFirstMediaUrl('featured_image') ?: null,
                ];
            })->toArray(),

            'attractions' => $property->attractions->map(function ($attr) {
                return [
                    'id' => $attr->id,
                    'name' => is_array($attr->name) ? ($attr->name['en'] ?? '') : $attr->name,
                    'description' => is_array($attr->description) ? ($attr->description['en'] ?? '') : $attr->description,
                    'image' => $attr->getFirstMediaUrl('featured_image') ?: null,
                ];
            })->toArray(),

            'offers' => $this->getBrandOffers($property),
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
}
