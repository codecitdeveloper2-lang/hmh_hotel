<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Property;
use App\Models\RoomType;
use App\Models\DiningOutlet;
use App\Models\Attraction;
use App\Models\Amenity;
use App\Models\FaqItem;

function downloadToUploads($url, $prefix = 'deira_') {
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
        Storage::disk('uploads')->put($filename, $contents);
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

echo "=== STARTING FULL CORAL DUBAI DEIRA HOTEL IMPORT ===\n";

// 1. PROPERTY
$brandId = Property::where('type', 'brand')->where('name->en', 'like', '%Coral%')->value('id') ?? 22;

$property = Property::updateOrCreate(
    ['slug' => 'coral-hotels-resorts-dubai-deira'],
    [
        'parent_id' => $brandId,
        'type' => 'hotel',
        'name' => ['en' => 'Coral Dubai Deira Hotel'],
        'intro_subtitle' => ['en' => 'You Are Unique for Us'],
        'intro_title' => ['en' => 'Welcome to Coral Dubai Deira Hotel'],
        'intro_text' => ['en' => '<p>Looking for a stylish and comfortable shopping getaway or corporate venue strategically located in the heart of Dubai? Our thoughtfully designed suites and rooms offer ample space for work or relaxation, while modern amenities provide all the comforts of home.</p>'],
        'description' => ['en' => '<p>Get Great 4 Star Hotel Deals at Coral Dubai Deira Hotel.</p><p>Looking for a stylish and comfortable shopping getaway or corporate venue strategically located in the heart of Dubai?</p><p>Our thoughtfully designed suites and rooms offer ample space for work or relaxation, while modern amenities provide all the comforts of home.</p><p>The Coral Dubai Deira Hotel is an ideal city centre location, located just 5 kilometres from Dubai International Airport.</p><p>Book your online hotel reservation directly with us today, or call us on <a href="tel:+97142248587">+971 4 224 8587</a> or email <a href="mailto:reservations.coraldeira@hmhhotelgroup.com">reservations.coraldeira@hmhhotelgroup.com</a></p>'],
        'city' => 'Dubai',
        'country' => 'United Arab Emirates',
        'address' => 'Al Muraqqabat Street, Deira, PO Box 82999, Dubai, UAE',
        'latitude' => '25.2631000',
        'longitude' => '55.3217000',
        'phone' => '+971 4 224 8587',
        'email' => 'reservations.coraldeira@hmhhotelgroup.com',
        'star_rating' => 4,
        'status' => 'live',
        'is_active' => 1,
        'is_featured' => 0,
        'meta_title' => ['en' => 'Coral Dubai Deira Hotel | 4-Star Hotel in Deira, Dubai'],
        'meta_description' => ['en' => 'Welcome to Coral Dubai Deira Hotel official site. Our 4-star hotel is ideally located in Deira, close to Dubai International Airport. Book now!'],
    ]
);

// Download Banner Images for Property
$bannerUrls = [
    'https://image-tc.galaxy.tf/wijpeg-1bzqowknh1dwd6pfomg5c0ett/lobby_standard.jpg',
    'https://image-tc.galaxy.tf/wijpeg-5id9knu8fydjfarqeok08hp9z/1-cdd-room.jpg',
    'https://image-tc.galaxy.tf/wijpeg-alny6hs21u8j3tuo2w101ll3x/3-cdd-water.jpg',
    'https://image-tc.galaxy.tf/wijpeg-6ylzjub2wkuai55nv0xjc009g/5.jpg',
    'https://image-tc.galaxy.tf/wijpeg-3e4g7y2j4to6f4brfj080b04c/pool.jpg'
];

$bannerFiles = [];
foreach ($bannerUrls as $bUrl) {
    $f = downloadToUploads($bUrl, 'banner_deira_');
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
        'slug' => 'deira-standard-king',
        'description' => 'Designed for effortless comfort, the Standard King Room features a plush king-size bed, sleek work desk, and contemporary city-inspired décor in the heart of Deira.',
        'size_sqm' => 28,
        'bed_type' => 'King Bed',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-6nht1kgqd231644lqocr5vicg/deluxe-room_standard.jpg',
        'sort_order' => 1,
    ],
    [
        'name' => 'Standard Twin',
        'slug' => 'deira-standard-twin',
        'description' => 'Featuring two comfortable twin beds and modern amenities, the Standard Twin Room provides a relaxing urban retreat for business travelers and holidaymakers.',
        'size_sqm' => 28,
        'bed_type' => 'Twin Beds',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-2n80oouoyj0loa57fejnbx01q/1_standard.jpg',
        'sort_order' => 2,
    ],
    [
        'name' => 'Standard King with Balcony',
        'slug' => 'deira-standard-king-balcony',
        'description' => 'Step out onto your private balcony overlooking the lively streets of Deira. Equipped with a king-size bed, seating area, and modern en-suite bathroom.',
        'size_sqm' => 32,
        'bed_type' => 'King Bed',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-1joav5ou26l82mxu3cxejoiij/club-room_standard.jpg',
        'sort_order' => 3,
    ],
    [
        'name' => 'Junior Suite',
        'slug' => 'deira-junior-suite',
        'description' => 'Offering extra space with an integrated lounge seating area, the Junior Suite is perfect for longer stays and executive travelers seeking refined comfort.',
        'size_sqm' => 42,
        'bed_type' => 'King Bed',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-1zysbbdzfic50mjkgj5axdak9/junior-suite_standard.jpg',
        'sort_order' => 4,
    ],
    [
        'name' => 'Executive Suite',
        'slug' => 'deira-executive-suite',
        'description' => 'Our Executive Suite boasts a separate living room, dining area, luxurious master bedroom, and premium guest amenities for a distinguished stay.',
        'size_sqm' => 55,
        'bed_type' => 'King Bed',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-aldl864ybtmn3w41injw7ahtm/executive-suite_standard.jpg',
        'sort_order' => 5,
    ],
    [
        'name' => 'Family Room',
        'slug' => 'deira-family-room',
        'description' => 'Spacious and accommodating, the Family Room offers interconnecting space and flexible bedding configurations to comfortably host parents and children in central Dubai.',
        'size_sqm' => 50,
        'bed_type' => '1 King + 2 Twin Beds',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-2nhtvvlnpv9dhi2bzj1b5m9zf/family-room_standard.jpg',
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
        'name' => 'Al Nafoora',
        'slug' => 'deira-al-nafoora',
        'cuisine_type' => 'International & Arabic Buffet',
        'opening_hours' => '06:30 AM - 11:00 PM',
        'description' => 'Al Nafoora offers a delightful culinary journey with vibrant international buffet spreads, authentic Middle Eastern specialties, and live interactive cooking stations.',
        'has_table_booking' => true,
        'image' => 'https://image-tc.galaxy.tf/wijpeg-43lvn1u2dsu7jynopdbt3g0in/al-nafoora_standard.jpg',
        'sort_order' => 1,
    ],
    [
        'name' => 'Rumours Café',
        'slug' => 'deira-rumours-cafe',
        'cuisine_type' => 'Café & Bakery',
        'opening_hours' => '07:00 AM - 11:00 PM',
        'description' => 'Located in the hotel lobby, Rumours Café is an intimate rendezvous for aromatic specialty coffees, artisanal teas, crisp salads, sandwiches, and freshly baked pastries.',
        'has_table_booking' => false,
        'image' => 'https://image-tc.galaxy.tf/wijpeg-d71jqzntdhnoxvqpfv2939f85/rumours-cafe_standard.jpg',
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


// 4. ATTRACTIONS (EXPLORE DUBAI)
echo "\n--- Seeding Attractions ---\n";
$attractionsData = [
    [
        'name' => 'Deira Clocktower',
        'slug' => 'deira-clocktower',
        'category' => 'Historic Landmark',
        'distance_from_hotel' => '5 mins',
        'description' => 'A prominent roundabout landmark standing as a timeless symbol of Dubai commercial heritage and a historic gateway to the city.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-bertzuq5zr4oss3ngbv4m2chz/deira-clocktower_standard.jpg',
        'sort_order' => 1,
    ],
    [
        'name' => 'Gold & Spice Souks',
        'slug' => 'deira-gold-spice-souks',
        'category' => 'Traditional Market',
        'distance_from_hotel' => '10 mins',
        'description' => 'Wander through the world-renowned traditional bazaars featuring sparkling gold jewelry, precious gems, exotic spices, herbs, and perfumes.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-cusjt50xbxealaxe31hunogll/gold-souk_standard.jpg',
        'sort_order' => 2,
    ],
    [
        'name' => 'Dubai Creek & Abra Rides',
        'slug' => 'deira-dubai-creek',
        'category' => 'Sightseeing & Waterway',
        'distance_from_hotel' => '8 mins',
        'description' => 'The historic saltwater inlet dividing Deira and Bur Dubai, offering picturesque views and authentic wooden Abra boat journeys across the water.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-cm5uyriivwcqwq9quuxdsas6p/dubai-creek_standard.jpg',
        'sort_order' => 3,
    ],
    [
        'name' => 'Al Fahidi Historic District & Museum',
        'slug' => 'deira-al-fahidi-district',
        'category' => 'Heritage & Culture',
        'distance_from_hotel' => '12 mins',
        'description' => 'One of the oldest heritage neighborhoods in Dubai with traditional wind towers, authentic art galleries, craft workshops, and museums.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-8k4jcmj2chkkr71yi86zdfh8f/al-fahidi-historic-neighborhood-al-bastakiya_standard.jpg',
        'sort_order' => 4,
    ],
    [
        'name' => 'Dubai Frame',
        'slug' => 'deira-dubai-frame',
        'category' => 'Modern Architecture',
        'distance_from_hotel' => '15 mins',
        'description' => 'An iconic 150-metre tall frame-shaped structure providing breathtaking panoramic vistas of Old Dubai on one side and the modern skyline on the other.',
        'image' => 'https://image-tc.galaxy.tf/wijpeg-v0vnpahytzoplf9mllpu2sd2/dubai-frame_standard.jpg',
        'sort_order' => 5,
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
        'name' => 'Rooftop Swimming Pool',
        'description' => 'Unwind under the sun in our temperature-controlled rooftop pool with comfortable sun loungers and panoramic city skyline views.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-3e4g7y2j4to6f4brfj080b04c/pool.jpg',
    ],
    [
        'name' => 'Fitness Centre',
        'description' => 'Stay in shape with our modern gym equipped with state-of-the-art cardiovascular machines, weight training equipment, and locker rooms.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-z5o2o919ek5waz6awo18rgru/dsc2681-enhanced-nr-edit-edit.jpg',
    ],
    [
        'name' => 'Sauna & Steam Rooms',
        'description' => 'Recharge and detoxify after a full day of meetings or shopping in separate sauna and steam bath facilities for men and women.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-t3o0q39zlj627byk1qts9dbm/dsc2825-edit.jpg',
    ],
    [
        'name' => 'Business Centre & Meetings',
        'description' => 'Professional meeting rooms and business services equipped with high-speed internet, projection facilities, and executive catering.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-6ylzjub2wkuai55nv0xjc009g/5.jpg',
    ],
    [
        'name' => 'Concierge & Tour Desk',
        'description' => '24-hour concierge assistance for city tours, airport transfers, desert safaris, dhow cruises, and restaurant bookings in Dubai.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-1bzqowknh1dwd6pfomg5c0ett/2-cdd-lobby.jpg',
    ],
];

$amenitiesList = [];
foreach ($amenityItems as $item) {
    $iconFile = downloadToUploads($item['image_url'], 'amenity_deira_');
    $amenitiesList[] = [
        'name' => $item['name'],
        'description' => $item['description'],
        'icon' => $iconFile,
    ];
}

$amenityModel = Amenity::updateOrCreate(
    ['property_id' => $property->id],
    [
        'title' => ['en' => 'Recreation & Comfort at Coral Dubai Deira Hotel'],
        'description' => 'Enjoy our rooftop swimming pool with city views, modern fitness centre, sauna, steam room, and corporate business facilities in Deira.',
        'read_more_label' => 'Explore Facilities',
        'read_more_link' => '#',
        'call_us_no' => '+971 4 224 8587',
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
        'question' => 'What are the check-in and check-out times at Coral Dubai Deira Hotel?',
        'answer' => 'Check-in is from 3:00 PM, and check-out is until 12:00 PM. Early check-in or late check-out can be requested subject to availability.',
        'sort_order' => 1,
    ],
    [
        'question' => 'How far is the hotel from Dubai International Airport?',
        'answer' => 'The hotel is conveniently situated in Deira, approximately 5 km (10 minutes driving time) from Dubai International Airport (DXB).',
        'sort_order' => 2,
    ],
    [
        'question' => 'Is there easy access to the Dubai Metro?',
        'answer' => 'Yes, both Al Rigga Metro Station and Salah Al Din Metro Station are within a short walking distance from the hotel.',
        'sort_order' => 3,
    ],
    [
        'question' => 'Does the hotel have a rooftop swimming pool and gym?',
        'answer' => 'Yes, Coral Dubai Deira features a temperature-controlled rooftop pool with city views, a fully-equipped gymnasium, and sauna/steam facilities.',
        'sort_order' => 4,
    ],
    [
        'question' => 'What dining venues are available at the hotel?',
        'answer' => 'Guests can enjoy international and Middle Eastern buffets at Al Nafoora, and freshly brewed coffees, teas, and pastries at Rumours Café.',
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

echo "\n=== ALL SECTIONS AND IMAGES SUCCESSFULLY IMPORTED FOR CORAL DUBAI DEIRA HOTEL! ===\n";
