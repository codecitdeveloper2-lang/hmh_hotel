<?php

use App\Models\Property;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

// 1. Create or get the Brand
$brandName = 'Bahi Hotels & Resorts';
$brand = Property::where('type', 'brand')->where('name->en', $brandName)->first();

if (!$brand) {
    $brand = Property::create([
        'type' => 'brand',
        'name' => ['en' => $brandName],
        'slug' => \Str::slug($brandName),
        'is_active' => true,
        'description' => ['en' => ''],
    ]);
    echo "Brand created.\n";
} else {
    echo "Brand exists.\n";
}

// 2. Download Image
$imageUrl = 'https://image-tc.galaxy.tf/wijpeg-52mj5yberbul5eij50qxg7mta/bahi-ajman-palace-hotel-our-hotels.jpg';
$imageName = 'bahi-ajman-palace-hotel.jpg';
$imagePath = public_path('uploads/' . $imageName);

if (!File::exists(public_path('uploads'))) {
    File::makeDirectory(public_path('uploads'), 0755, true);
}

if (!File::exists($imagePath)) {
    $imageContent = Http::get($imageUrl)->body();
    File::put($imagePath, $imageContent);
    echo "Image downloaded.\n";
} else {
    echo "Image already exists.\n";
}

// 3. Create or update the Hotel
$hotelName = 'Bahi Ajman Palace Hotel';
$hotel = Property::where('type', 'hotel')->where('name->en', $hotelName)->first();

$hotelData = [
    'parent_id' => $brand->id,
    'type' => 'hotel',
    'name' => ['en' => $hotelName],
    'slug' => 'bahi-hotels-resorts-bahi-ajman-palace',
    'description' => ['en' => 'Welcome to Bahi Ajman Palace Hotel Official Site. Our Ajman 5-star hotel offers a haven of tranquility overlooking the Arabian Gulf. Book now!'],
    'meta_title' => ['en' => 'Bahi Ajman Palace Hotel | 5-Star Hotel in Ajman'],
    'meta_description' => ['en' => 'Welcome to Bahi Ajman Palace Hotel Official Site. Our Ajman 5-star hotel offers a haven of tranquility overlooking the Arabian Gulf. Book now!'],
    'address' => 'Sheikh Humaid Bin Rashid Al Nuaimi Street, PO Box 7176, Ajman, United Arab Emirates',
    'city' => 'Ajman',
    'country' => 'United Arab Emirates',
    'latitude' => 25.41682,
    'longitude' => 55.43877,
    'phone' => '+971 6 701 8888',
    'email' => 'reservations.bahiajman@hmhhotelgroup.com',
    'is_active' => true,
    'cover_image' => $imageName,
    'banner_images' => json_encode([$imageName]),
];

if (!$hotel) {
    Property::create($hotelData);
    echo "Hotel created successfully.\n";
} else {
    $hotel->update($hotelData);
    echo "Hotel updated successfully.\n";
}
