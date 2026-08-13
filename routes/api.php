<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DestinationApiController;

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
