<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComingSoonApiController extends Controller
{
    public function index()
    {
        $pages = \App\Models\Page::where('is_active', 1)->get();
        $allSections = [];

        foreach ($pages as $page) {
            $bodyData = is_array($page->body) ? ($page->body['en'] ?? '') : $page->body;
            $decodedBody = json_decode((string)$bodyData, true) ?? [];
            if (isset($decodedBody['coming_soon_sections']) && is_array($decodedBody['coming_soon_sections'])) {
                foreach ($decodedBody['coming_soon_sections'] as &$section) {
                    // Prepend full URL for image if necessary
                    if (!empty($section['image'])) {
                        $section['image_url'] = url('uploads/' . $section['image']);
                    } else {
                        $section['image_url'] = null;
                    }
                    $allSections[] = $section;
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $allSections
        ]);
    }
}
