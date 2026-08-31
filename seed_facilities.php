<?php
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Amenity;

$json = '{
  "main_section": {
    "title": "Relax at our Ajman spa hotel",
    "description": "Make the most of your stay, with a wide range of five-star facilities and leisure amenities, combined with exceptional service and warm hospitality.",
    "cta": {
      "label": "Book Now"
    },
    "phone": "+971 6 701 8888"
  },
  "facilities": [
    {
      "name": "Spa",
      "description": "Our luxury spa housed within Bahi Ajman Palace Hotel features nine lavishly designed treatment rooms fitted with highly engineered furniture for the ultimate Spa experience.",
      "icon_url": "https://image-tc.galaxy.tf/wisvg-f62nu2f0nr7onprvogd18wnn/spa_logo.svg"
    },
    {
      "name": "Fitness Centre",
      "description": "Enjoy complimentary access to the Fitness Centre when you stay at Bahi Ajman Palace Hotel. Our modern gym facility in Ajman is fully equipped with an extensive variety of state-of-the-art exercise machines to meet every fitness level.",
      "icon_url": "https://image-tc.galaxy.tf/wisvg-8frobb5iq8j189we29jvxkd6e/fitness-centre_logo.svg"
    },
    {
      "name": "Swimming Pool",
      "description": "We encourage guests to unwind and recharge by taking a dip in our relaxing temperature-controlled outdoor infinity pool.",
      "icon_url": "https://image-tc.galaxy.tf/wisvg-uq7n4u2813p78ld7hsvq0fj5/swimming-pool_logo.svg"
    },
    {
      "name": "Health Club Membership",
      "description": "Become a member of our Health Club and benefit from exclusive use of the resort\'s private beach, as well as the hotel\'s luxury facilities such as the pool, fitness centre, sauna, steam room, jacuzzi, lockers, and towels.",
      "icon_url": "https://image-tc.galaxy.tf/wisvg-8frobb5iq8j189we29jvxkd6e/fitness-centre_logo.svg"
    },
    {
      "name": "Day Beach Access",
      "description": "Complimentary access to the beach and pool is available for guests staying at the hotel.",
      "icon_url": "https://image-tc.galaxy.tf/wisvg-uq7n4u2813p78ld7hsvq0fj5/swimming-pool_logo.svg"
    },
    {
      "name": "Swimming Lessons",
      "description": "Our professional swimming coach can teach you all the basic strokes, as well as working with advanced swimmers to improve your swimming skills.",
      "icon_url": "https://image-tc.galaxy.tf/wisvg-uq7n4u2813p78ld7hsvq0fj5/swimming-pool_logo.svg"
    },
    {
      "name": "Kids Club",
      "description": "Children are offered a great environment where they can play and experience fun time. Our Kids Club is open to in-house guests as complimentary and available from 10:00am to 6:00pm.",
      "icon_url": "https://image-tc.galaxy.tf/wisvg-8frobb5iq8j189we29jvxkd6e/fitness-centre_logo.svg"
    }
  ]
}';

$data = json_decode($json, true);

// Delete existing amenities for property 29
Amenity::where('property_id', 29)->delete();

$amenitiesList = [];

foreach ($data['facilities'] as $facility) {
    $iconPath = null;
    if (!empty($facility['icon_url'])) {
        $contents = file_get_contents($facility['icon_url']);
        if ($contents) {
            $filename = uniqid('icon_') . '.svg';
            // Save to public disk
            Storage::disk('public')->put($filename, $contents);
            $iconPath = $filename;
        }
    }

    $amenitiesList[] = [
        'name' => $facility['name'],
        'description' => $facility['description'],
        'icon' => $iconPath
    ];
}

$a = Amenity::create([
    'property_id' => 29,
    'title' => ['en' => $data['main_section']['title']],
    'description' => $data['main_section']['description'],
    'read_more_label' => $data['main_section']['cta']['label'],
    'read_more_link' => '#',
    'call_us_no' => $data['main_section']['phone'],
    'amenities_list' => $amenitiesList,
    'is_active' => 1,
    'display_order' => 0
]);

echo "Successfully seeded amenities!\n";
