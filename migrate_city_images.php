<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Migrate all cities that have a city_image stored in private storage
$cities = \App\Models\City::whereNotNull('city_image')->where('city_image', '!=', '')->get();

echo "Found " . $cities->count() . " cities with city_image.\n\n";

foreach ($cities as $city) {
    $filename = $city->city_image;

    // Skip if already in a path format (already migrated)
    if (str_contains($filename, '/') || str_contains($filename, '\\')) {
        echo "City {$city->id}: already has path format '{$filename}' - skipping\n";
        continue;
    }

    $srcPath  = storage_path("app/private/{$filename}");
    $destDir  = storage_path("app/public/city-images");
    $destPath = "{$destDir}/{$filename}";
    $newValue = "city-images/{$filename}";

    echo "City {$city->id}: {$filename}\n";

    if (!file_exists($srcPath)) {
        echo "  Source NOT FOUND at: {$srcPath}\n\n";
        continue;
    }

    // Make directory if needed
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }

    if (copy($srcPath, $destPath)) {
        $city->city_image = $newValue;
        $city->save();
        echo "  SUCCESS: Copied to {$destPath}\n";
        echo "  Updated city_image to: {$newValue}\n\n";
    } else {
        echo "  ERROR: Failed to copy!\n\n";
    }
}

echo "Done!\n";
