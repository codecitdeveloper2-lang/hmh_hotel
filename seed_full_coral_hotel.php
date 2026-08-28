<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\RoomType;
use App\Models\DiningOutlet;
use App\Models\Attraction;
use App\Models\Amenity;
use App\Models\FaqItem;

function downloadToDisk($url, $disk = 'uploads', $prefix = 'coral_') {
    if (empty($url)) return null;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $contents = curl_exec($ch);
    curl_close($ch);

    if ($contents) {
        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
        $filename = uniqid($prefix) . '.' . $ext;
        Storage::disk($disk)->put($filename, $contents);
        return $filename;
    }
    return null;
}

function attachSpatieMedia($model, $url, $collection = 'featured_image', $disk = 'uploads') {
    if (empty($url)) return;
    try {
        $model->clearMediaCollection($collection);
        $model->addMediaFromUrl($url)
              ->toMediaCollection($collection, $disk);
    } catch (\Throwable $e) {
        echo "Media error: " . $e->getMessage() . "\n";
    }
}

echo "=== STARTING FULL CORAL BEACH RESORT SHARJAH IMPORT ===\n";

// 1. PROPERTY
$brandId = Property::where('type', 'brand')->where('name->en', 'like', '%Coral%')->value('id') ?? 22;

$property = Property::updateOrCreate(
    ['slug' => 'coral-hotels-resorts-beach-sharjah'],
    [
        'parent_id' => $brandId,
        'type' => 'hotel',
        'name' => ['en' => 'Coral Beach Resort Sharjah'],
        'intro_subtitle' => ['en' => 'You Are Unique for Us'],
        'intro_title' => ['en' => 'Welcome to Coral Beach Resort Sharjah'],
        'intro_text' => ['en' => '<p>Looking for a luxury beach getaway on the stunning coastline of the Arabian Gulf? Nestled along the tranquil shoreline, the Coral Beach Resort Sharjah offers a haven of relaxation and enjoyment for guests of all ages.</p>'],
        'description' => ['en' => '<p>Looking for a luxury beach getaway on the stunning coastline of the Arabian Gulf?</p><p>Nestled along the tranquil shoreline, the Coral Beach Resort Sharjah offers a haven of relaxation and enjoyment for guests of all ages.</p><p>Located just 17 kilometres from Sharjah International Airport and 24 kilometres from Dubai International Airport, our Sharjah family hotel is the perfect choice for family retreats and corporate stays.</p><p>Book hotel rooms online or by calling <a href="tel:+97165229999">+971 6 522 9999</a></p>'],
        'city' => 'Sharjah',
        'country' => 'United Arab Emirates',
        'address' => 'Corniche Street, Al Muntazah, PO Box 5525, Sharjah, UAE',
        'latitude' => '25.4095000',
        'longitude' => '55.4328000',
        'phone' => '+971 6 522 9999',
        'email' => 'reservations.cbrs@hmhhotelgroup.com',
        'star_rating' => 4,
        'status' => 'live',
        'is_active' => 1,
        'is_featured' => 0,
        'meta_title' => ['en' => 'Coral Beach Resort Sharjah | 4-Star Hotel in Sharjah'],
        'meta_description' => ['en' => 'Welcome to Coral Beach Resort Sharjah official site. Our Sharjah family hotel offers a haven of relaxation and enjoyment in the United Arab Emirates. Book now!'],
    ]
);

// Download Banner Images for Property
$bannerUrls = [
    'https://image-tc.galaxy.tf/wijpeg-3t536yj35tbdogp4bb75tkxa6/pool_standard.jpg',
    'https://image-tc.galaxy.tf/wijpeg-kgqfehfx8qerfybjbopr9mvd/3-coral-beach-resort-sharjah23.jpg',
    'https://image-tc.galaxy.tf/wijpeg-adf3fec2w81leze8plf7y1kj/beach-view-sunset-1.jpg',
    'https://image-tc.galaxy.tf/wijpeg-enc89dedyy954iqqitf6kh1s3/exterior-view-1.jpg',
    'https://image-tc.galaxy.tf/wijpeg-2lloftteuo7hin1caui5m81mx/beach-view-at-coral-beach-resort-sharjah_portrait.jpg'
];

$bannerFiles = [];
foreach ($bannerUrls as $bUrl) {
    $f = downloadToDisk($bUrl, 'uploads', 'banner_');
    if ($f) $bannerFiles[] = $f;
}
if (!empty($bannerFiles)) {
    $property->banner_images = $bannerFiles;
    $property->cover_image = $bannerFiles[0];
    $property->save();
}
echo "Property configured: ID {$property->id} with " . count($bannerFiles) . " banner images.\n";


// 2. ROOM TYPES
echo "\n--- Seeding Room Types ---\n";
$roomsData = [
    [
        'name' => 'Standard King',
        'slug' => 'standard-king',
        'description' => 'The Standard King Room with a city view is the ideal retreat for guests looking to explore the city while enjoying the tranquility of a beachfront location. Relish the breathtaking panoramic views of the city from the comfort of one of our Standard Rooms.',
        'size_sqm' => 30,
        'bed_type' => 'King Bed',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-a6tvgdv91150usyf1y37w5hwa/standard-city-view-room_standard.jpg',
        'sort_order' => 1,
    ],
    [
        'name' => 'Standard Twin',
        'slug' => 'standard-twin',
        'description' => 'Our Standard Twin Room offers a comfortable stay with two twin beds, modern amenities, and a relaxing atmosphere, perfect for both business and leisure.',
        'size_sqm' => 30,
        'bed_type' => 'Twin Beds',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-444vxeiogqk5436eliuysoxwn/coral-beach-resort-sharjah-standard-twin_standard.jpg',
        'sort_order' => 2,
    ],
    [
        'name' => 'Standard King Room with a View',
        'slug' => 'standard-king-room-with-a-view',
        'description' => 'Offering stunning views over the azure Arabian Gulf, our Standard King Room with a View provides elegance, comfort, and peaceful beachfront relaxation.',
        'size_sqm' => 30,
        'bed_type' => 'King Bed',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-4l6btlvg65mp3bw3y00cz54td/standard-sea-view-room_standard.jpg',
        'sort_order' => 3,
    ],
    [
        'name' => 'Standard Twin Room with a View',
        'slug' => 'standard-twin-room-with-a-view',
        'description' => 'Enjoy spectacular views of the sea from our Standard Twin Room with a View, equipped with modern conveniences and comfortable twin bedding.',
        'size_sqm' => 30,
        'bed_type' => 'Twin Beds',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-1jq64d00cwhuyotrgkj9994l8/coral-beach-reosrtstandard-twin-with-view_standard.jpg',
        'sort_order' => 4,
    ],
    [
        'name' => 'Family Room City View',
        'slug' => 'family-room-city-view',
        'description' => 'Designed with families in mind, the Family Room City View offers ample living space, comfortable bedding, and convenient amenities for parents and children.',
        'size_sqm' => 45,
        'bed_type' => '1 King + 2 Twin Beds',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-9tg9iozxkflv3qc0v6kg926l0/family-room-city-view_standard.jpg',
        'sort_order' => 5,
    ],
    [
        'name' => 'Family Room with a View',
        'slug' => 'family-room-with-a-view',
        'description' => 'Spacious and luminous, the Family Room with a View overlooks the serene waters of the gulf, providing the ultimate coastal retreat for your family vacation.',
        'size_sqm' => 45,
        'bed_type' => '1 King + 2 Twin Beds',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-7pcz7qa4rihvnhu19zybkd8kv/family-room-sea-view_standard.jpg',
        'sort_order' => 6,
    ],
];

foreach ($roomsData as $r) {
    $room = RoomType::updateOrCreate(
        ['property_id' => $property->id, 'slug' => $r['slug']],
        [
            'name' => ['en' => $r['name']],
            'description' => ['en' => $r['description']],
            'size_sqm' => $r['size_sqm'],
            'bed_type' => $r['bed_type'],
            'is_active' => 1,
            'sort_order' => $r['sort_order'],
            'read_more_label' => 'READ MORE',
        ]
    );
    attachSpatieMedia($room, $r['image'], 'featured_image', 'uploads');
    echo "  Room seeded: {$r['name']}\n";
}


// 3. DINING OUTLETS
echo "\n--- Seeding Dining Outlets ---\n";
$diningData = [
    [
        'name' => 'Al Dente',
        'slug' => 'al-dente',
        'cuisine_type' => 'Italian',
        'opening_hours' => '12:30 PM - 11:00 PM',
        'description' => 'Al Dente serves authentic Italian cuisine in a warm, rustic setting. Indulge in classic pastas, wood-fired pizzas, and exquisite desserts prepared by our master chefs.',
        'has_table_booking' => true,
        'image' => 'https://image-tc.galaxy.tf/wijpeg-1eysqd9xrgq6xcwbm659nirrc/al-dente_standard.jpg',
        'sort_order' => 1,
    ],
    [
        'name' => 'Côté Jardin',
        'slug' => 'cote-jardin',
        'cuisine_type' => 'International Buffet',
        'opening_hours' => '06:30 AM - 10:30 PM',
        'description' => 'Côté Jardin offers an extensive international buffet for breakfast, lunch, and dinner, featuring live cooking stations and vibrant flavors from around the globe.',
        'has_table_booking' => false,
        'image' => 'https://image-tc.galaxy.tf/wijpeg-cy3trmnjc3cqtl6k3xpb89ubd/cote-jardin_standard.jpg',
        'sort_order' => 2,
    ],
    [
        'name' => 'Casa Samak Seafood Restaurant',
        'slug' => 'casa-samak-seafood-restaurant',
        'cuisine_type' => 'Fresh Seafood',
        'opening_hours' => '12:00 PM - 11:00 PM',
        'description' => 'Located right on the beach, Casa Samak offers fresh seafood caught daily, prepared to your taste with panoramic sunset views over the Arabian Gulf.',
        'has_table_booking' => true,
        'image' => 'https://image-tc.galaxy.tf/wijpeg-bzcu3m5g5cx0pj2z11nd1wonn/casa-samak_standard.jpg',
        'sort_order' => 3,
    ],
    [
        'name' => 'Rumours Café',
        'slug' => 'rumours-cafe',
        'cuisine_type' => 'Café & Bakery',
        'opening_hours' => '07:00 AM - 11:00 PM',
        'description' => 'Rumours Café is the perfect spot for freshly brewed artisan coffees, gourmet teas, handcrafted sandwiches, light snacks, and decadent homemade pastries.',
        'has_table_booking' => false,
        'image' => 'https://image-tc.galaxy.tf/wijpeg-8qkf2xjdru3tdiztkk9n6ordj/rumours-cafe_standard.jpg',
        'sort_order' => 4,
    ],
    [
        'name' => 'Waves',
        'slug' => 'waves',
        'cuisine_type' => 'Pool & Beach Bar',
        'opening_hours' => '09:00 AM - 07:00 PM',
        'description' => 'Relax by the pool or on the beach with refreshing tropical mocktails, chilled smoothies, and delicious casual bites at Waves pool and beach bar.',
        'has_table_booking' => false,
        'image' => 'https://image-tc.galaxy.tf/wijpeg-77a5555aerza4h6kxqh5wm0je/waves_standard.jpg',
        'sort_order' => 5,
    ],
];

foreach ($diningData as $d) {
    $dining = DiningOutlet::updateOrCreate(
        ['property_id' => $property->id, 'slug' => $d['slug']],
        [
            'name' => ['en' => $d['name']],
            'description' => ['en' => $d['description']],
            'cuisine_type' => ['en' => $d['cuisine_type']],
            'opening_hours' => ['en' => $d['opening_hours']],
            'has_table_booking' => $d['has_table_booking'],
            'is_active' => 1,
            'sort_order' => $d['sort_order'],
        ]
    );
    attachSpatieMedia($dining, $d['image'], 'featured_image', 'uploads');
    echo "  Dining seeded: {$d['name']}\n";
}


// 4. ATTRACTIONS (EXPLORE SHARJAH)
echo "\n--- Seeding Attractions ---\n";
$attractionsData = [
    [
        'name' => 'Noor Island',
        'slug' => 'noor-island',
        'category' => 'Sightseeing',
        'distance_from_hotel' => '15 mins',
        'description' => 'A magical lagoon island featuring stunning art installations, a tropical butterfly house, peaceful landscaped gardens, and luminous illuminated sculptures.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-5vtllcxw4zxzie0b79onummxy/noor-island_standard.jpg',
        'sort_order' => 1,
    ],
    [
        'name' => 'Sharjah Grand Mosque',
        'slug' => 'sharjah-grand-mosque',
        'category' => 'Culture & Heritage',
        'distance_from_hotel' => '20 mins',
        'description' => 'One of the largest and most magnificent mosques in the UAE, showcasing breathtaking Islamic and Ottoman architecture with towering minarets and intricate domes.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-dvz1ufj5uypi14iuf1dorb4jg/sharjah-grand-mosque_standard.jpg',
        'sort_order' => 2,
    ],
    [
        'name' => 'Al Qasba',
        'slug' => 'qasba',
        'category' => 'Entertainment',
        'distance_from_hotel' => '15 mins',
        'description' => 'A vibrant canal-side cultural and entertainment destination featuring waterfront restaurants, cafes, art galleries, musical fountains, and traditional boat tours.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-6l3a99r179om5epu9wfo5aeqw/qasba_standard.jpg',
        'sort_order' => 3,
    ],
    [
        'name' => 'Sharjah Souk (Central Market)',
        'slug' => 'sharjah-souk',
        'category' => 'Shopping',
        'distance_from_hotel' => '12 mins',
        'description' => 'Also known as the Blue Souk, this iconic Islamic-inspired landmark houses over 600 shops selling gold, fine jewelry, traditional perfumes, carpets, and local handicrafts.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-aleoxfg9k7hqdpclix1ew7gj7/sharjah-souk_standard.jpg',
        'sort_order' => 4,
    ],
];

foreach ($attractionsData as $a) {
    $attraction = Attraction::updateOrCreate(
        ['property_id' => $property->id, 'slug' => $a['slug']],
        [
            'name' => ['en' => $a['name']],
            'description' => ['en' => $a['description']],
            'category' => $a['category'],
            'distance_from_hotel' => $a['distance_from_hotel'],
            'is_active' => 1,
            'sort_order' => $a['sort_order'],
        ]
    );
    attachSpatieMedia($attraction, $a['image'], 'featured_image', 'uploads');
    echo "  Attraction seeded: {$a['name']}\n";
}


// 5. AMENITIES SECTION
echo "\n--- Seeding Amenities Section ---\n";
$amenityItems = [
    [
        'name' => 'Private Beach Access',
        'description' => 'Complimentary direct access to our pristine private sandy beach along the Arabian Gulf with sun loungers, umbrellas, and beachside towel service.',
        'icon_url' => 'https://image-tc.galaxy.tf/wijpeg-adf3fec2w81leze8plf7y1kj/beach-view-sunset-1.jpg',
    ],
    [
        'name' => 'Infinity Swimming Pools',
        'description' => 'Two expansive temperature-controlled outdoor pools, including dedicated water slides for children and an adults-only relaxation zone.',
        'icon_url' => 'https://image-tc.galaxy.tf/wijpeg-3t536yj35tbdogp4bb75tkxa6/pool_standard.jpg',
    ],
    [
        'name' => 'Fitness Centre',
        'description' => 'Stay active in our fully-equipped modern gymnasium featuring state-of-the-art cardiovascular machines, free weights, and fitness instructors.',
        'icon_url' => 'https://image-tc.galaxy.tf/wijpeg-8sfgwu7xgewrntq52kbu7u1hj/8-coral-beach-gym-1017-v2.jpg',
    ],
    [
        'name' => 'Spa & Wellness',
        'description' => 'Relax and rejuvenate with our signature therapeutic massages, body treatments, sauna, steam rooms, and bubbling hot tubs.',
        'icon_url' => 'https://image-tc.galaxy.tf/wijpeg-bswdmtorwdhuzeeyx6gn7w1hd/coral-beach-7902flare.jpg',
    ],
    [
        'name' => 'Kids Club',
        'description' => 'A vibrant, secure, and fun-filled environment offering interactive games, creative arts and crafts, and supervised activities for young guests.',
        'icon_url' => 'https://image-tc.galaxy.tf/wijpeg-8dlj91a32oqhok5nrv34rgrbi/3-coral-beach-resort-sharjah23.jpg',
    ],
    [
        'name' => 'Tennis & Sports Courts',
        'description' => 'Enjoy floodlit tennis courts, badminton, beach volleyball, and table tennis facilities suited for friendly matches or family tournaments.',
        'icon_url' => 'https://image-tc.galaxy.tf/wijpeg-ahrxeesl4u9hqj3is194pui23/img-3567.jpg',
    ],
];

$amenitiesList = [];
foreach ($amenityItems as $item) {
    $iconFile = downloadToDisk($item['icon_url'], 'public', 'amenity_icon_');
    $amenitiesList[] = [
        'name' => $item['name'],
        'description' => $item['description'],
        'icon' => $iconFile,
    ];
}

$amenityModel = Amenity::updateOrCreate(
    ['property_id' => $property->id],
    [
        'title' => ['en' => 'Relax & Rejuvenate at Coral Beach Resort Sharjah'],
        'description' => 'Make the most of your stay with a wide array of resort amenities, private beach access, temperature-controlled pools, and recreation facilities for all ages.',
        'read_more_label' => 'Explore Facilities',
        'read_more_link' => '#',
        'call_us_no' => '+971 6 522 9999',
        'amenities_list' => $amenitiesList,
        'is_active' => 1,
        'display_order' => 0,
    ]
);
echo "Amenities section seeded with " . count($amenitiesList) . " items.\n";


// 6. FAQS
echo "\n--- Seeding FAQs ---\n";
$faqsData = [
    [
        'question' => 'What are the check-in and check-out times at Coral Beach Resort Sharjah?',
        'answer' => 'Check-in is from 3:00 PM, and check-out is until 12:00 PM. Early check-in and late check-out can be arranged based on availability.',
        'sort_order' => 1,
    ],
    [
        'question' => 'Does the hotel have private beach access?',
        'answer' => 'Yes, Coral Beach Resort Sharjah features a private sandy beach on the Arabian Gulf with complimentary sun loungers and towel service for all hotel guests.',
        'sort_order' => 2,
    ],
    [
        'question' => 'How far is the resort from the nearest airport?',
        'answer' => 'The resort is located approximately 17 km (20 minutes) from Sharjah International Airport and 24 km (30 minutes) from Dubai International Airport.',
        'sort_order' => 3,
    ],
    [
        'question' => 'What dining options are available at the resort?',
        'answer' => 'The resort offers 5 distinct dining venues: Al Dente (Italian), Côté Jardin (International Buffet), Casa Samak (Fresh Seafood), Rumours Café (Pastries & Coffee), and Waves (Pool & Beach Bar).',
        'sort_order' => 4,
    ],
    [
        'question' => 'Is complimentary parking available on-site?',
        'answer' => 'Yes, complimentary valet and secure self-parking are provided on-site for all in-house guests and restaurant patrons.',
        'sort_order' => 5,
    ],
];

foreach ($faqsData as $faq) {
    FaqItem::updateOrCreate(
        ['property_id' => $property->id, 'sort_order' => $faq['sort_order']],
        [
            'question' => ['en' => $faq['question']],
            'answer' => ['en' => $faq['answer']],
            'sort_order' => $faq['sort_order'],
        ]
    );
    echo "  FAQ seeded: {$faq['question']}\n";
}

echo "\n=== ALL SECTIONS AND IMAGES SUCCESSFULLY IMPORTED FOR CORAL BEACH RESORT SHARJAH! ===\n";
