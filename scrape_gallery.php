<?php
use Illuminate\Support\Facades\Storage;
use App\Models\Amenity;

$html = file_get_contents('https://www.hmhhotelgroup.com/bahi-hotels-resorts-bahi-ajman-palace');
preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/[^\"\'\s]+\.(jpg|webp|png)/i', $html, $matches);
$urls = array_unique($matches[0]);

$amenityImages = [];
// Based on the screenshot the user uploaded, it's a picture of the pool / resort exterior. 
// We will grab the first 3-4 images that seem appropriate.
// Since we don't know exactly which ones are the amenities, we'll just download a few unique high-res ones.
$count = 0;
foreach($urls as $url) {
    if (strpos($url, 'logo') !== false || strpos($url, 'icon') !== false) continue;
    $count++;
    
    $contents = @file_get_contents($url);
    if ($contents) {
        $filename = uniqid('gallery_') . '.jpg';
        Storage::disk('public')->put($filename, $contents);
        $amenityImages[] = $filename;
    }
    
    if ($count >= 5) break; // Limit to 5 images for the gallery
}

// Update the amenity record for property 29
$a = Amenity::where('property_id', 29)->first();
if ($a) {
    $a->gallery = $amenityImages;
    $a->save();
    echo "Gallery updated with " . count($amenityImages) . " images.";
} else {
    echo "Amenity for property 29 not found.";
}
