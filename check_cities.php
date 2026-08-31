<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\City;
use App\Models\Destination;

echo "=== CITIES ===\n";
foreach (City::all() as $c) {
    echo "ID: {$c->id} | Name: " . json_encode($c->getTranslations('name')) . " | Slug: {$c->slug} | Country: {$c->country}\n";
}

echo "\n=== DESTINATIONS ===\n";
foreach (Destination::all() as $d) {
    echo "ID: {$d->id} | Name: " . json_encode($d->getTranslations('name')) . " | Slug: {$d->slug}\n";
}
