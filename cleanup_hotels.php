<?php
use App\Models\Property;

Property::find(30)?->delete();

$p = Property::find(31);
if ($p) {
    $p->slug = 'coral-beach-resort-sharjah';
    $p->save();
}

echo "Current hotels:\n";
foreach (Property::where('type', 'hotel')->get() as $hotel) {
    echo "ID: {$hotel->id} | Name: " . (is_array($hotel->name) ? $hotel->name['en'] : $hotel->name) . " | Slug: {$hotel->slug}\n";
}
