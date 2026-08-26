<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OfferApiController extends Controller
{
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

        // Transform the offers to include full image URLs and hotel slug
        $transformed = $offers->map(function ($offer) {
            $data = $offer->toArray();
            if (!empty($data['banner_image'])) {
                $data['banner_image'] = str_starts_with($data['banner_image'], 'http') 
                    ? $data['banner_image'] 
                    : url('/uploads/' . ltrim($data['banner_image'], '/'));
            } else {
                // Fallback to Spatie Media if needed
                $mediaUrl = $offer->getFirstMediaUrl('banner_image');
                if ($mediaUrl) {
                    $data['banner_image'] = $mediaUrl;
                }
            }
            
            // Get the hotel slug for frontend routing
            $property = \App\Models\Property::find($offer->hotel);
            $data['hotel_slug'] = $property ? $property->slug : 'offers';
            
            // Format gallery images
            if (!empty($data['images']) && is_array($data['images'])) {
                $data['images'] = array_map(function ($img) {
                    return url('/uploads/' . $img);
                }, $data['images']);
            }
            
            return $data;
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

        $data = $offer->toArray();
        if (!empty($data['banner_image'])) {
            $data['banner_image'] = str_starts_with($data['banner_image'], 'http') 
                ? $data['banner_image'] 
                : url('/uploads/' . ltrim($data['banner_image'], '/'));
        } else {
            $mediaUrl = $offer->getFirstMediaUrl('banner_image');
            if ($mediaUrl) {
                $data['banner_image'] = $mediaUrl;
            }
        }
        
        if (!empty($data['images']) && is_array($data['images'])) {
            $data['images'] = array_map(function ($img) {
                return str_starts_with($img, 'http') ? $img : url('/uploads/' . ltrim($img, '/'));
            }, $data['images']);
        }
        
        $property = \App\Models\Property::find($offer->hotel);
        $data['hotel_slug'] = $property ? $property->slug : 'offers';
        
        $data['brand_logo'] = null;
        if ($property && $property->parent_id) {
            $brand = \App\Models\Property::find($property->parent_id);
            if ($brand && $brand->logo) {
                $data['brand_logo'] = url('/uploads/' . ltrim($brand->logo, '/'));
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}



