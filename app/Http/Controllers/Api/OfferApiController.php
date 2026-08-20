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
        // Get active offers ordered by sort_order
        $offers = Offer::where('is_active', 1)
            ->where('status', 'Published') // Assuming status is 'Published'
            ->orderBy('sort_order', 'asc')
            ->get();

        // Transform the offers to include full image URLs and hotel slug
        $transformed = $offers->map(function ($offer) {
            $data = $offer->toArray();
            if (!empty($data['banner_image'])) {
                $data['banner_image'] = url('/uploads/' . $data['banner_image']);
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
            $data['banner_image'] = url('/uploads/' . $data['banner_image']);
        }
        
        if (!empty($data['images']) && is_array($data['images'])) {
            $data['images'] = array_map(function ($img) {
                return url('/uploads/' . $img);
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



