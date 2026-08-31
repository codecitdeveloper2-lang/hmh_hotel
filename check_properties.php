<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
$props = Property::all(['id', 'slug', 'name', 'type', 'parent_id', 'city', 'country']);
foreach ($props as $p) {
    echo "ID: {$p->id} | Type: {$p->type} | Parent: {$p->parent_id} | Slug: {$p->slug} | City: {$p->city} | Country: {$p->country} | Name: " . json_encode($p->getTranslations('name')) . "\n";
}
