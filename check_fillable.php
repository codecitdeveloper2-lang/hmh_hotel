<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (['Amenity', 'Attraction', 'DiningOutlet', 'FaqItem', 'GalleryItem', 'MeetingEventPage', 'Offer', 'Property', 'RoomType'] as $m) {
    $class = "App\\Models\\$m";
    $inst = new $class();
    echo "Model: $m => Fillable: " . json_encode($inst->getFillable()) . "\n";
}
