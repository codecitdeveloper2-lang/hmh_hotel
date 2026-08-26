<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$offers = \App\Models\Offer::get(['id', 'name', 'is_active', 'status']);
foreach ($offers as $offer) {
    echo "ID: " . $offer->id . ", Name: " . json_encode($offer->name) . ", Active: " . $offer->is_active . ", Status: " . $offer->status . "\n";
}
