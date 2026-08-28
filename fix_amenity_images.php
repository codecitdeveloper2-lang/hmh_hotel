<?php

use Illuminate\Support\Facades\Storage;
use App\Models\Amenity;

function downloadToUploads($url, $prefix = 'amenity_coral_') {
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

$amenityItems = [
    [
        'name' => 'Private Beach Access',
        'description' => 'Complimentary direct access to our pristine private sandy beach along the Arabian Gulf with sun loungers, umbrellas, and beachside towel service.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-adf3fec2w81leze8plf7y1kj/beach-view-sunset-1.jpg',
    ],
    [
        'name' => 'Infinity Swimming Pools',
        'description' => 'Two expansive temperature-controlled outdoor pools, including dedicated water slides for children and an adults-only relaxation zone.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-3t536yj35tbdogp4bb75tkxa6/pool_standard.jpg',
    ],
    [
        'name' => 'Fitness Centre',
        'description' => 'Stay active in our fully-equipped modern gymnasium featuring state-of-the-art cardiovascular machines, free weights, and fitness instructors.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-8sfgwu7xgewrntq52kbu7u1hj/8-coral-beach-gym-1017-v2.jpg',
    ],
    [
        'name' => 'Spa & Wellness',
        'description' => 'Relax and rejuvenate with our signature therapeutic massages, body treatments, sauna, steam rooms, and bubbling hot tubs.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-bswdmtorwdhuzeeyx6gn7w1hd/coral-beach-7902flare.jpg',
    ],
    [
        'name' => 'Kids Club',
        'description' => 'A vibrant, secure, and fun-filled environment offering interactive games, creative arts and crafts, and supervised activities for young guests.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-8dlj91a32oqhok5nrv34rgrbi/3-coral-beach-resort-sharjah23.jpg',
    ],
    [
        'name' => 'Tennis & Sports Courts',
        'description' => 'Enjoy floodlit tennis courts, badminton, beach volleyball, and table tennis facilities suited for friendly matches or family tournaments.',
        'image_url' => 'https://image-tc.galaxy.tf/wijpeg-ahrxeesl4u9hqj3is194pui23/img-3567.jpg',
    ],
];

$amenitiesList = [];
foreach ($amenityItems as $item) {
    $iconFile = downloadToUploads($item['image_url']);
    $amenitiesList[] = [
        'name' => $item['name'],
        'description' => $item['description'],
        'icon' => $iconFile,
    ];
    echo "Downloaded amenity image: {$iconFile} for {$item['name']}\n";
}

$amenity = Amenity::where('property_id', 31)->first();
if ($amenity) {
    $amenity->amenities_list = $amenitiesList;
    $amenity->save();
    echo "Successfully updated Amenity record for Property 31 with real uploaded images!\n";
}
