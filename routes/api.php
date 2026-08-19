<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DestinationApiController;
use App\Http\Controllers\Api\PageApiController;

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
