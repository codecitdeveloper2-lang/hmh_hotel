<?php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\RoomType;
use App\Models\DiningOutlet;
use App\Models\Amenity;

function fetchHtml($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

function extractImages($html, $limit = 5) {
    preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/[^\"\'\s]+\.(jpg|webp|png)/i', $html, $matches);
    $urls = array_unique($matches[0] ?? []);
    
    $downloaded = [];
    $count = 0;
    foreach($urls as $url) {
        if (stripos($url, 'logo') !== false || stripos($url, 'icon') !== false) continue;
        
        $contents = @file_get_contents($url);
        if ($contents) {
            $filename = uniqid('coral_') . '.jpg';
            Storage::disk('public')->put($filename, $contents);
            $downloaded[] = $filename;
            $count++;
            if ($count >= $limit) break;
        }
    }
    return $downloaded;
}

echo "Starting Coral Beach Resort Sharjah extraction...\n";

// 1. PROPERTY
$homeHtml = fetchHtml('https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah');
$propertyName = 'Coral Beach Resort Sharjah';
$propertySlug = Str::slug($propertyName);

// Try to find the intro text
preg_match('/<p class="mb-4 text-gray-700">(.*?)<\/p>/s', $homeHtml, $introMatch);
$introText = $introMatch[1] ?? 'A family-friendly haven with excellent amenities.';

$brandId = Property::where('type', 'brand')->where('name->en', 'like', '%Coral%')->value('id') ?? 22;

$property = Property::firstOrCreate(
    ['slug' => $propertySlug],
    [
        'parent_id' => $brandId,
        'type' => 'hotel',
        'name' => ['en' => $propertyName],
        'description' => ['en' => strip_tags($introText)],
        'intro_title' => ['en' => 'Welcome to Coral Beach Resort Sharjah'],
        'intro_text' => ['en' => strip_tags($introText)],
        'city' => 'Sharjah',
        'country' => 'United Arab Emirates',
        'is_active' => 1,
        'star_rating' => 4,
    ]
);

echo "Property created: ID {$property->id}\n";

$mainImages = extractImages($homeHtml, 5);
if (!empty($mainImages)) {
    $property->banner_images = $mainImages;
    $property->cover_image = $mainImages[0];
    $property->save();
}

// 2. ROOMS
echo "Extracting Rooms...\n";
$roomsHtml = fetchHtml('https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/rooms');
// Basic regex to find room titles (this is very naive but often works for these templates)
preg_match_all('/<h3[^>]*>(.*?)<\/h3>/s', $roomsHtml, $roomTitles);
$titles = array_unique(array_map('strip_tags', $roomTitles[1] ?? []));

foreach ($titles as $idx => $title) {
    $title = trim($title);
    if (empty($title) || strlen($title) > 50 || stripos($title, 'rooms') !== false) continue;
    
    $roomImages = extractImages($roomsHtml, 2); // Get some random images for rooms as placeholders
    
    $room = RoomType::where('property_id', $property->id)->where('name->en', $title)->first();
    if (!$room) {
        $room = RoomType::create([
            'property_id' => $property->id,
            'name' => ['en' => $title],
            'description' => ['en' => "Experience luxury and comfort in our $title."],
            'max_occupancy' => 2,
            'size' => rand(30, 60),
            'bed_type' => 'King / Twin',
            'view' => 'City or Sea View',
            'is_active' => 1,
            'display_order' => $idx,
            'features' => ['en' => 'Free WiFi, TV, Minibar, Air Conditioning'],
            'banner_images' => $roomImages
        ]);
    }
    echo "  Added Room: $title\n";
}

// 3. DINING
echo "Extracting Dining...\n";
$diningHtml = fetchHtml('https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/dining');
preg_match_all('/<h3[^>]*>(.*?)<\/h3>/s', $diningHtml, $diningTitles);
$dTitles = array_unique(array_map('strip_tags', $diningTitles[1] ?? []));

foreach ($dTitles as $idx => $title) {
    $title = trim($title);
    if (empty($title) || strlen($title) > 50 || stripos($title, 'dining') !== false) continue;
    
    $diningImages = extractImages($diningHtml, 1);
    
    $dining = DiningOutlet::where('property_id', $property->id)->where('name->en', $title)->first();
    if (!$dining) {
        $dining = DiningOutlet::create([
            'property_id' => $property->id,
            'name' => ['en' => $title],
            'description' => ['en' => "Savor delectable dishes at $title, offering a unique culinary experience."],
            'cuisine' => ['en' => 'International'],
            'timing' => ['en' => '07:00 AM - 11:00 PM'],
            'dress_code' => ['en' => 'Smart Casual'],
            'is_active' => 1,
            'display_order' => $idx,
            'cover_image' => $diningImages[0] ?? null
        ]);
    }
    echo "  Added Dining: $title\n";
}

// 4. AMENITIES
echo "Extracting Amenities...\n";
$amenityHtml = fetchHtml('https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/facilities');
preg_match_all('/<h3[^>]*>(.*?)<\/h3>/s', $amenityHtml, $amenityTitles);
$aTitles = array_unique(array_map('strip_tags', $amenityTitles[1] ?? []));

$amenitiesList = [];
foreach ($aTitles as $idx => $title) {
    $title = trim($title);
    if (empty($title) || strlen($title) > 50 || stripos($title, 'facilities') !== false) continue;
    
    $amenitiesList[uniqid()] = [
        'name' => $title,
        'description' => "Enjoy our top-tier $title facility.",
        'icon' => null
    ];
}

$amenityImages = extractImages($amenityHtml, 5);
$a = Amenity::firstOrCreate(
    ['property_id' => $property->id],
    [
        'title' => ['en' => 'Hotel Facilities'],
        'description' => 'A comprehensive range of five-star facilities to enhance your stay.',
        'is_active' => 1,
        'display_order' => 0,
        'call_us_no' => '+971 6 522 9999',
    ]
);

if (!empty($amenitiesList)) {
    $a->amenities_list = $amenitiesList;
}
if (!empty($amenityImages)) {
    $a->gallery = $amenityImages;
}
$a->save();
echo "  Added Amenities overview with " . count($amenitiesList) . " items.\n";

echo "Extraction complete!\n";
