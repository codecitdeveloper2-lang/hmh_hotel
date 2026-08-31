<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\RoomType;
use App\Models\DiningOutlet;
use App\Models\Attraction;
use App\Models\Amenity;
use App\Models\FaqItem;

function downloadToDisk($url, $disk = 'uploads', $prefix = 'jubail_') {
    if (empty($url)) return null;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
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
        echo "Media error for {$collection}: " . $e->getMessage() . "\n";
    }
}

echo "=== STARTING FULL CORAL JUBAIL HOTEL IMPORT ===\n";

// 1. PROPERTY
$brandId = Property::where('type', 'brand')->where('name->en', 'like', '%Coral%')->value('id') ?? 22;

$property = Property::updateOrCreate(
    ['slug' => 'coral-jubail-hotel'],
    [
        'parent_id' => $brandId,
        'type' => 'hotel',
        'name' => ['en' => 'Coral Jubail Hotel'],
        'intro_subtitle' => ['en' => 'You Are Unique for Us'],
        'intro_title' => ['en' => 'Welcome to Coral Jubail Hotel'],
        'intro_text' => ['en' => '<p>Ideally situated in the heart of Jubail along the serene Arabian Gulf corniche on Al Shati Road, Coral Jubail Hotel offers four-star comfort, world-class business and leisure amenities, stylish accommodations, and heartfelt hospitality.</p>'],
        'description' => ['en' => '<p>Get Great 4 Star Hotel Deals at Coral Jubail Hotel.</p><p>Ideally situated in the heart of Jubail along the serene Arabian Gulf corniche on Al Shati Road, Coral Jubail Hotel offers four-star comfort, world-class business and leisure amenities, stylish accommodations, and heartfelt hospitality.</p><p>Whether traveling for business in the Jubail Industrial City or planning a relaxing seaside staycation, our hotel provides modern rooms and expansive multi-bedroom suites, temperature-controlled indoor swimming pool, fitness center, sauna, steam room, and refined dining venues.</p><p>Book your hotel stay directly online with us today, or call us on <a href="tel:+966133628800">+966 13 362 8800</a> or email <a href="mailto:central.reservations@hmhhotelgroup.com">central.reservations@hmhhotelgroup.com</a></p>'],
        'city' => 'Jubail',
        'country' => 'Saudi Arabia',
        'address' => 'Al Shati Road - Exit 8, Jubail, Kingdom of Saudi Arabia',
        'latitude' => '27.0112000',
        'longitude' => '49.6589000',
        'phone' => '+966 13 362 8800',
        'email' => 'central.reservations@hmhhotelgroup.com',
        'star_rating' => 4,
        'status' => 'live',
        'check_in_time' => '16:00:00',
        'check_out_time' => '12:00:00',
        'is_active' => 1,
        'is_featured' => 0,
        'meta_title' => ['en' => 'Coral Jubail Hotel | 4-Star Hotel in Jubail, Saudi Arabia'],
        'meta_description' => ['en' => 'Welcome to Coral Jubail Hotel official site. Discover 4-star luxury accommodation along the Jubail Corniche with indoor pool, gym, and dining. Book now!'],
    ]
);

// Download Banner Images for Property
$bannerUrls = [
    'https://image-tc.galaxy.tf/wijpeg-1swfikan3e5c84kiz0za91pgn/coral-jubail-hotel_standard.jpg',
    'https://image-tc.galaxy.tf/wijpeg-7t0kmamc5y6dmw9zsqbuh607j/coral-jubail-br.jpg',
    'https://image-tc.galaxy.tf/wijpeg-6ui9fziq4j8f4ujov88eh6ce9/1-cjh-lobby.jpg',
    'https://image-tc.galaxy.tf/wijpeg-efyqdwm33n4nvec88g81cfhtk/coral-jubail-reception.jpg',
    'https://image-tc.galaxy.tf/wijpeg-363izdmdolzdhndxdz3h4br4y/coral-jubail-rest.jpg',
    'https://image-tc.galaxy.tf/wijpeg-7a82y6ypkihsvzwv8csdoyt7m/coral-jubail-sea-view.jpg'
];

$bannerFiles = [];
foreach ($bannerUrls as $bUrl) {
    $f = downloadToDisk($bUrl, 'uploads', 'banner_jubail_');
    if ($f) $bannerFiles[] = $f;
}
if (!empty($bannerFiles)) {
    $property->banner_images = $bannerFiles;
    $property->cover_image = $bannerFiles[0];
    $property->save();
}

// Spatie media collections on Property
attachSpatieMedia($property, $bannerUrls[0], 'cover_image', 'uploads');
foreach ($bannerUrls as $bUrl) {
    try {
        $property->addMediaFromUrl($bUrl)->toMediaCollection('hero_images', 'uploads');
        $property->addMediaFromUrl($bUrl)->toMediaCollection('gallery', 'uploads');
    } catch (\Throwable $e) {}
}

echo "Property configured: ID {$property->id} ({$property->slug}) with " . count($bannerFiles) . " banner images.\n";


// 2. ROOM TYPES
echo "\n--- Seeding Room Types ---\n";
$roomsData = [
    [
        'name' => 'Standard Rooms',
        'slug' => 'jubail-standard-rooms',
        'description' => 'Book your Standard Room at the four-star Coral Jubail Hotel and enjoy a welcoming and spacious 25 square meters accommodation with a choice of king or twin beds, modern en-suite bathroom, high-speed Wi-Fi, and city or corniche views.',
        'size_sqm' => 25,
        'bed_type' => 'Choice of King or Twin Beds',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-7t0kmamc5y6dmw9zsqbuh607j/standard-rooms_wide.jpg',
        'sort_order' => 1,
    ],
    [
        'name' => 'Premium Room',
        'slug' => 'jubail-premium-room',
        'description' => 'Stay in a Premium Room to experience the best of Coral Jubail Hotel’s unrivalled hospitality. Our comfortable accommodation spanning across 27-35 square meters features all you need for a calm and relaxing stay with a comfortable sitting area and choice of king or queen-size beds.',
        'size_sqm' => 35,
        'bed_type' => 'King or Queen-size Bed',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-634qr5x8hk2a2ob3pgh20oibv/premium-room_wide.jpg',
        'sort_order' => 2,
    ],
    [
        'name' => 'Executive Suite',
        'slug' => 'jubail-executive-suite',
        'description' => 'Stay in one of our 37 square meter Executive Suites and benefit from all the facilities you need for a truly relaxing stay. Our one-bedroom Executive Suites provide you with all the luxury of a hotel with the comfort and privacy of your own separate living room.',
        'size_sqm' => 37,
        'bed_type' => 'King Bed with Private Living Room',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-3ygg1d3t3oc7yxivycbeirxxw/executive-suite_wide.jpg',
        'sort_order' => 3,
    ],
    [
        'name' => 'Coral Suites',
        'slug' => 'jubail-coral-suites',
        'description' => 'Discover the impressive Coral Suite which unfolds across 53 square meters of space. Featuring two Master Bedrooms with a spacious and luxurious living room, the stunning Coral suite is a great choice for families or larger groups seeking elevated comfort.',
        'size_sqm' => 53,
        'bed_type' => '2 Master Bedrooms (King & Twin)',
        'image' => 'https://image-tc.galaxy.tf/wipng-b7fnmbiqsagzjoecfdqgz8ebe/coral-suites_wide.png',
        'sort_order' => 4,
    ],
    [
        'name' => 'Coral Grand Suite',
        'slug' => 'jubail-coral-grand-suite',
        'description' => 'Experience the epitome of luxury living at the four-star Coral Jubail Hotel in our 116 square meter four-bedroom Grand Suite. As a multi-bedroom suite, it is ideal for large groups and families, providing a great long-stay solution. Four Master Bedrooms and three spacious living areas ensure plenty of room for everyone.',
        'size_sqm' => 116,
        'bed_type' => '4 Master Bedrooms & 3 Living Areas',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-7pi56jqyvxpceuphkqxrgx60g/coral-grand-suite-3_wide.jpg',
        'sort_order' => 5,
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
    attachSpatieMedia($room, $r['image'], 'gallery', 'uploads');
    echo "  Room seeded: {$r['name']}\n";
}


// 3. DINING OUTLETS
echo "\n--- Seeding Dining Outlets ---\n";
$diningData = [
    [
        'name' => 'Al Nafoora',
        'slug' => 'jubail-al-nafoora',
        'cuisine_type' => 'International & Middle Eastern Buffet',
        'opening_hours' => 'Breakfast: 06:00 - 10:30 | Lunch: 12:30 - 15:30 | Dinner: 19:00 - 23:00',
        'description' => 'Al Nafoora invites you to savor a rich variety of international and Middle Eastern flavors. Featuring an expansive daily buffet, live interactive cooking stations, and an extensive à la carte menu prepared with fresh premium ingredients.',
        'has_table_booking' => true,
        'image' => 'https://image-tc.galaxy.tf/wijpeg-363izdmdolzdhndxdz3h4br4y/al-nafoora_wide.jpg',
        'sort_order' => 1,
    ],
    [
        'name' => 'Rumours Café',
        'slug' => 'jubail-rumours-cafe',
        'cuisine_type' => 'Café, Artisanal Pastries & Beverages',
        'opening_hours' => 'Daily: 07:00 AM - 11:00 PM',
        'description' => 'The perfect setting for informal business meetings or leisurely catch-ups. Indulge in artisanal coffees, fine teas, freshly squeezed fruit juices, delicate pastries, and light gourmet bites in a sophisticated atmosphere.',
        'has_table_booking' => false,
        'image' => 'https://image-tc.galaxy.tf/wijpeg-b2j4qgf2hoyun0kr8th4glacl/rumours-cafe_wide.jpg',
        'sort_order' => 2,
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


// 4. ATTRACTIONS (EXPLORE JUBAIL)
echo "\n--- Seeding Attractions ---\n";
$attractionsData = [
    [
        'name' => 'Al Fanateer Corniche',
        'slug' => 'jubail-al-fanateer-corniche',
        'category' => 'Waterfront & Promenade',
        'distance_from_hotel' => '10 mins',
        'description' => 'A picturesque coastal promenade in Jubail featuring lush green landscaped gardens, pedestrian walkways, sandy beaches, seaside cafes, and boat cruises along the Arabian Gulf.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-7a82y6ypkihsvzwv8csdoyt7m/coral-jubail-sea-view.jpg',
        'sort_order' => 1,
    ],
    [
        'name' => 'King Abdulaziz Center for World Culture (Ithra)',
        'slug' => 'jubail-king-abdulaziz-center',
        'category' => 'Culture & Arts',
        'distance_from_hotel' => '50 mins',
        'description' => 'An iconic architectural landmark and world-renowned cultural institution featuring a multi-level museum, modern library, performing arts theater, and interactive innovation exhibits.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-9r10wnp2ft03219mabj0rbwr4/exterior-photo-1-rev.jpg',
        'sort_order' => 2,
    ],
    [
        'name' => 'Historic Jubail Heritage & Port',
        'slug' => 'jubail-historic-heritage',
        'category' => 'Heritage & History',
        'distance_from_hotel' => '5 mins',
        'description' => 'Explore the rich heritage and maritime roots of Jubail, once a prominent historic fishing and pearling settlement that transformed into the global industrial and coastal hub of today.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-1qjk32nqqi4l1ytq1mn7lbeei/coral-jubail-ext.jpg',
        'sort_order' => 3,
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
        'name' => 'Indoor Swimming Pool',
        'description' => 'Take a refreshing dip in our temperature-controlled indoor swimming pool, offering a calm and relaxing environment for year-round swimming.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-7a82y6ypkihsvzwv8csdoyt7m/coral-jubail-sea-view.jpg',
    ],
    [
        'name' => 'Gym & Fitness Center',
        'description' => 'Keep up with your fitness routine in our well-equipped gym featuring modern cardiovascular machines, free weights, and workout equipment.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-c6ksxg33rm22mioupyeevenze/gym_wide.jpg',
    ],
    [
        'name' => 'Sauna & Steam Rooms',
        'description' => 'Unwind and detoxify after a busy day in our dedicated sauna and steam bath facilities designed for optimal relaxation and wellness.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-4s6lrh1nuwagattbre2rjnqus/spa_wide.jpg',
    ],
    [
        'name' => 'Business Center & Meeting Facilities',
        'description' => 'Versatile meeting rooms with high-speed Wi-Fi, modern audio-visual technology, and professional business support for corporate events.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-773vxvxvdlhmusij63vjhivf6/majlis-meet-jubail-2.jpg',
    ],
    [
        'name' => 'Corniche Waterfront Location',
        'description' => 'Directly located on Al Shati Road along the beautiful Jubail Corniche, providing scenic Gulf views and leisurely seaside walking access.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-1swfikan3e5c84kiz0za91pgn/coral-jubail-hotel_standard.jpg',
    ],
];

$amenitiesList = [];
foreach ($amenityItems as $item) {
    $iconFile = downloadToDisk($item['image_url'], 'uploads', 'amenity_jubail_');
    $amenitiesList[] = [
        'name' => $item['name'],
        'description' => $item['description'],
        'icon' => $iconFile,
    ];
}

$amenityModel = Amenity::updateOrCreate(
    ['property_id' => $property->id],
    [
        'title' => ['en' => 'Comfort & Recreation at Coral Jubail Hotel'],
        'description' => 'Enjoy our temperature-controlled indoor swimming pool, fully-equipped gym, sauna, steam room, and corporate business facilities on the Jubail Corniche.',
        'read_more_label' => 'Explore Facilities',
        'read_more_link' => '#',
        'call_us_no' => '+966 13 362 8800',
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
        'question' => 'Where is Coral Jubail Hotel located?',
        'answer' => 'The hotel is centrally located on Al Shati Road - Exit 8, offering easy access to the Jubail City Center, the Industrial Area, and directly along the Jubail Corniche.',
        'sort_order' => 1,
    ],
    [
        'question' => 'Is the hotel close to the Jubail Corniche?',
        'answer' => 'Yes, the hotel is situated directly on the Jubail Corniche, making it ideal for guests who enjoy scenic Gulf sea views and relaxing evening walks.',
        'sort_order' => 2,
    ],
    [
        'question' => 'How far is the hotel from Jubail Industrial City?',
        'answer' => 'The hotel is approximately a 15–20 minute drive from the main Jubail Industrial City (Royal Commission), making it a preferred choice for business travelers and contractors.',
        'sort_order' => 3,
    ],
    [
        'question' => 'How far is the hotel from King Fahd International Airport (DMM)?',
        'answer' => 'The hotel is approximately 80 km from King Fahd International Airport in Dammam, which is roughly a 45–60 minute highway drive.',
        'sort_order' => 4,
    ],
    [
        'question' => 'Can the hotel arrange airport transfers?',
        'answer' => 'Yes, airport pick-up and drop-off services can be arranged for an additional fee. Please provide your flight details to our reservations team at least 24 hours in advance.',
        'sort_order' => 5,
    ],
    [
        'question' => 'What are the check-in and check-out times?',
        'answer' => 'Check-in is from 4:00 PM, and check-out is until 12:00 PM (Noon). Early check-in and late check-out can be requested subject to room availability.',
        'sort_order' => 6,
    ],
    [
        'question' => 'Does the hotel serve alcohol?',
        'answer' => 'No. In accordance with local regulations in the Kingdom of Saudi Arabia, Coral Jubail is a dry hotel. Alcohol is not served or permitted on premises.',
        'sort_order' => 7,
    ],
    [
        'question' => 'What dining venues and breakfast options are available?',
        'answer' => 'Guests can enjoy an extensive international and Middle Eastern daily buffet with live cooking at Al Nafoora Restaurant, specialty coffees and pastries at Rumours Café, plus 24-hour in-room dining.',
        'sort_order' => 8,
    ],
    [
        'question' => 'Does the hotel have a swimming pool and wellness facilities?',
        'answer' => 'Yes, Coral Jubail features a temperature-controlled indoor swimming pool for year-round recreation, a modern fitness gymnasium, sauna, and steam room.',
        'sort_order' => 9,
    ],
    [
        'question' => 'Is parking and Wi-Fi complimentary?',
        'answer' => 'Yes, high-speed Wi-Fi and secure on-site private parking are provided complimentary for all hotel guests.',
        'sort_order' => 10,
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

echo "\n=== ALL SECTIONS AND IMAGES SUCCESSFULLY IMPORTED FOR CORAL JUBAIL HOTEL! ===\n";

