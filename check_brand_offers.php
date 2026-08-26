<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$brands = ['new-brand-test', 'brand-name-new1', 'wednesday-brand', 'tuesday-brand'];
foreach ($brands as $slug) {
    $brand = \App\Models\Property::where('slug', $slug)->first();
    if ($brand) {
        echo "Brand: {$brand->name} (ID: {$brand->id})\n";
        $childIds = $brand->children->pluck('id')->toArray();
        echo "Child IDs: " . implode(', ', $childIds) . "\n";
        
        $offers = \App\Models\Offer::whereIn('hotel', $childIds)->get();
        echo "Offers count for child hotels: " . $offers->count() . "\n";

        // Check pivot table
        $pivotOffers = $brand->offers;
        echo "Pivot offers count: " . $pivotOffers->count() . "\n";
        echo "-------------------\n";
    }
}
