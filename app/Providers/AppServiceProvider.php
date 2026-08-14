<?php

namespace App\Providers;

use App\Models\City;
use App\Models\Destination;
use App\Models\SeoMetadata;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Auth\StaticUserProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Auth::provider('static', function ($app, array $config) {
            return new StaticUserProvider();
        });

        $flushDestinationCache = static function (): void {
            Cache::tags(['destinations'])->flush();
        };

        Destination::saved($flushDestinationCache);
        Destination::deleted($flushDestinationCache);

        City::saved($flushDestinationCache);
        City::deleted($flushDestinationCache);

        SeoMetadata::saved($flushDestinationCache);
        SeoMetadata::deleted($flushDestinationCache);

        Media::saved(function (Media $media) use ($flushDestinationCache): void {
            if ($media->model_type === Destination::class) {
                $flushDestinationCache();
            }
        });

        Media::deleted(function (Media $media) use ($flushDestinationCache): void {
            if ($media->model_type === Destination::class) {
                $flushDestinationCache();
            }
        });
    }
}
