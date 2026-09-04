<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OurLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OurLocationController extends Controller
{
    /**
     * Display a listing of active/featured locations.
     */
    public function index(Request $request): JsonResponse
    {
        // Get active locations ordered by display_order with destination relationship
        $locations = OurLocation::with('destination')
            ->where('featured_on_home', true)
            ->orderBy('display_order', 'asc')
            ->get();

        // Transform the locations to include full image URLs and destination_slug
        $transformed = $locations->map(function ($location) {
            $data = $location->toArray();
            if (!empty($data['home_image'])) {
                // Since images are saved directly in public/uploads/
                $data['home_image'] = url('/uploads/' . ltrim($data['home_image'], '/'));
            }
            if ($location->destination) {
                $data['destination_slug'] = $location->destination->slug;
            }
            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => $transformed
        ]);
    }
}
