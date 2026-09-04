<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DestinationApiController;
use App\Http\Controllers\Api\PageApiController;
use App\Http\Controllers\Api\OfferApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Destinations API (Public — no auth required)
|--------------------------------------------------------------------------
*/
Route::prefix('destinations')->group(function () {
    Route::get('/', [DestinationApiController::class, 'index']);
    Route::get('/{slug}', [DestinationApiController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Pages API (Public — no auth required)
|--------------------------------------------------------------------------
*/
Route::prefix('pages')->group(function () {
    Route::get('/', [PageApiController::class, 'index']);
    Route::get('/{slug}', [PageApiController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Properties API (Public — no auth required)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Api\PropertyApiController;
Route::prefix('properties')->group(function () {
    Route::get('/brands', [PropertyApiController::class, 'getBrandsAndHotels']);
    Route::get('/hotels', [PropertyApiController::class, 'getAllHotels']);
    Route::get('/{slug}', [PropertyApiController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Offers API (Public — no auth required)
|--------------------------------------------------------------------------
*/
Route::prefix('offers')->group(function () {
    Route::get('/types', [OfferApiController::class, 'getTypes']);
    Route::get('/', [OfferApiController::class, 'index']);
    Route::get('/{slug}', [OfferApiController::class, 'show']);
});

use App\Http\Controllers\Api\OurLocationController;
Route::get('/our-locations', [OurLocationController::class, 'index']);

use App\Http\Controllers\Api\ComingSoonApiController;
Route::get('/coming-soon', [ComingSoonApiController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Meetings & Events API (Public — no auth required)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Api\MeetingEventApiController;
Route::get('/properties/{hotelSlug}/meetings-events', [MeetingEventApiController::class, 'getByProperty']);
Route::get('/properties/{hotelSlug}/meetings-events/{eventSlug}', [MeetingEventApiController::class, 'showByProperty']);

Route::prefix('meetings-events')->group(function () {
    Route::get('/', [MeetingEventApiController::class, 'index']);
    Route::get('/{slug}', [MeetingEventApiController::class, 'show']);
});

