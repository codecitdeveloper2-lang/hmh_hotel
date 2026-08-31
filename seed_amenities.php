<?php
$amenities = [
    ['title' => 'Spa', 'category' => 'Spa', 'image' => 'https://image-tc.galaxy.tf/wisvg-f62nu2f0nr7onprvogd18wnn/spa_logo.svg', 'display_order' => 1],
    ['title' => 'Fitness Centre', 'category' => 'Gym', 'image' => 'https://image-tc.galaxy.tf/wisvg-8frobb5iq8j189we29jvxkd6e/fitness-centre_logo.svg', 'display_order' => 2],
    ['title' => 'Swimming Pool', 'category' => 'Swimming Pool', 'image' => 'https://image-tc.galaxy.tf/wisvg-uq7n4u2813p78ld7hsvq0fj5/swimming-pool_logo.svg', 'display_order' => 3],
    ['title' => 'Health Club Membership', 'category' => 'Gym', 'display_order' => 4],
    ['title' => 'Day Beach Access', 'category' => 'Hotel Exterior', 'display_order' => 5],
    ['title' => 'Swimming Lessons', 'category' => 'Swimming Pool', 'display_order' => 6],
    ['title' => 'Kids Club', 'category' => 'Events', 'display_order' => 7],
];
foreach($amenities as $data) {
    $a = \App\Models\Amenity::create([
        'property_id' => 29,
        'title' => ['en' => $data['title']],
        'category' => $data['category'],
        'display_order' => $data['display_order'],
        'is_active' => 1
    ]);
    if(isset($data['image'])) {
        $a->addMediaFromUrl($data['image'])->toMediaCollection('featured_image', 'uploads');
    }
}
echo "Done seeding amenities!\n";
