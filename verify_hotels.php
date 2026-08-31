<?php
use App\Models\Property;
use App\Models\RoomType;
use App\Models\DiningOutlet;
use App\Models\Attraction;
use App\Models\Amenity;
use App\Models\FaqItem;

echo "=== CURRENT HOTELS SUMMARY ===\n";
foreach (Property::where('type', 'hotel')->get() as $h) {
    echo "ID: {$h->id} | Name: {$h->display_name} | Slug: {$h->slug}\n";
    echo "  - Rooms: " . RoomType::where('property_id', $h->id)->count() . "\n";
    echo "  - Dining: " . DiningOutlet::where('property_id', $h->id)->count() . "\n";
    echo "  - Attractions: " . Attraction::where('property_id', $h->id)->count() . "\n";
    echo "  - Amenities: " . Amenity::where('property_id', $h->id)->count() . "\n";
    echo "  - FAQs: " . FaqItem::where('property_id', $h->id)->count() . "\n";
}
