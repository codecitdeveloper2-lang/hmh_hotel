<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/test-brands', function () {
    $brands = [
        ['id' => 13, 'name' => 'tuesday_brand', 'brand' => 'test', 'country' => 'test', 'city' => 'test', 'star_rating' => 'test', 'status' => 'Live', 'last_updated' => 'test', 'slug' => 'slug', 'star_segment' => 'N/A', 'sort_order' => 0]
    ];
    return view('filament.pages.manage-brands', [
        'brands' => $brands,
        'from' => 1,
        'to' => 1,
        'totalItems' => 1,
        'currentPage' => 1,
        'lastPage' => 1,
        'perPage' => 10
    ]);
});
