<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\GalleryItem;
use App\Models\MeetingEventPage;

echo "Gallery items count: " . GalleryItem::count() . "\n";
foreach (GalleryItem::all() as $g) {
    echo "ID: {$g->id} | Property: {$g->property_id} | Caption: {$g->caption}\n";
}

echo "\nMeeting & Event pages count: " . MeetingEventPage::count() . "\n";
foreach (MeetingEventPage::all() as $m) {
    echo "ID: {$m->id} | Property: {$m->property_id} | Title: {$m->title}\n";
}
