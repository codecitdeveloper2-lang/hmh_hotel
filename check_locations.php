<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OurLocation;
echo "OurLocation count: " . OurLocation::count() . "\n";
foreach (OurLocation::all() as $loc) {
    echo "ID: {$loc->id} | Name: " . json_encode($loc->name) . " | City: {$loc->city} | Country: {$loc->country} | Slug: {$loc->slug}\n";
}
