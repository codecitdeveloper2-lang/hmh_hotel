<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$city = \App\Models\City::find(6);
echo "city_image value: " . $city->city_image . "\n";

// Check where the file lives
$paths = [
    'storage/app/public/'   => storage_path('app/public/' . $city->city_image),
    'storage/app/private/'  => storage_path('app/private/' . $city->city_image),
    'public/storage/'       => public_path('storage/' . $city->city_image),
];

foreach ($paths as $label => $p) {
    echo $label . ": " . (file_exists($p) ? "EXISTS -> {$p}" : "NOT FOUND") . "\n";
}
