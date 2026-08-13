<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dests = \App\Models\Destination::all();
foreach ($dests as $d) {
    echo $d->id . ': ' . $d->getRawOriginal('name') . "\n";
}
