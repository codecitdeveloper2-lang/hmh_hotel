<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
$p = Property::find(31);
echo "Attributes for ID 31 (Coral Beach Resort Sharjah):\n";
print_r($p->toArray());
