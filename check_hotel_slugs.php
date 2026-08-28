<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
$props = Property::where('type', 'hotel')->get(['id', 'slug', 'name', 'parent_id', 'city', 'country']);
print_r($props->toArray());
