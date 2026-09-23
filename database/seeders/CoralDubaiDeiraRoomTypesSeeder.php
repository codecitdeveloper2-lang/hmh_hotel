<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Support\Str;

class CoralDubaiDeiraRoomTypesSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Property::find(32);
        if (!$hotel) {
            $this->command->error("Hotel with ID 32 not found!");
            return;
        }

        $this->command->info("Seeding Room Types for Hotel 32: " . (is_array($hotel->name) ? $hotel->name['en'] : $hotel->name));

        $tempDir = storage_path('app/temp_rooms');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $rooms = [
            [
                'id' => 38,
                'name' => 'Standard King',
                'slug' => 'deira-standard-king',
                'short_description' => 'Experience unparalleled comfort and stunning city views in our 33-square-meter Standard King room. Featuring a king-size bed, this spacious retreat offers the perfect blend of relaxation and style, ensuring a memorable stay.',
                'description' => '<p>Experience unparalleled comfort and stunning city views in our 33-square-meter Standard King room. Featuring a king-size bed, this spacious retreat offers the perfect blend of relaxation and style, ensuring a memorable stay.</p>',
                'size_sqm' => 33,
                'bed_type' => 'King Bed',
                'travelclick_roomtype_id' => '535023',
                'starting_price' => 'FROM AED 332.50',
                'read_more_label' => 'READ MORE',
                'read_more_link' => '/coral-hotels-resorts-dubai-deira/rooms-suites/standard-king',
                'book_now_label' => 'BOOK NOW',
                'book_now_link' => 'https://reservations.travelclick.com/115806?HotelId=115806&languageid=1&rooms=1&adults=1&roomtypeid=535023',
                'meta_title' => 'Standard King | Coral Dubai Deira Hotel',
                'meta_description' => 'Stay in our Standard King at Coral Dubai Deira Hotel. Book your hotel stay now!',
                'sort_order' => 1,
                'special_features' => [
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Air Condition'],
                    ['icon' => 'heroicon-o-tv', 'feature_name' => 'Flat-screen TV'],
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Free Toiletries'],
                    ['icon' => 'heroicon-o-wifi', 'feature_name' => 'Complimentary Wi-Fi'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'Safety Deposit Box'],
                    ['icon' => 'heroicon-o-gift', 'feature_name' => 'Minibar'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'In-room Safe'],
                    ['icon' => 'heroicon-o-bolt', 'feature_name' => 'Hairdryer'],
                    ['icon' => 'heroicon-o-wrench-screwdriver', 'feature_name' => 'Ironing Board'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Tea/Coffee Making Facilities'],
                    ['icon' => 'heroicon-o-tv', 'feature_name' => 'Satellite TV'],
                    ['icon' => 'heroicon-o-bell', 'feature_name' => 'Wake-up service'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Kettle'],
                ],
                'featured_images' => [
                    'https://image-tc.galaxy.tf/wijpeg-6nht1kgqd231644lqocr5vicg/deluxe-room_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-d4ibbjrvkpfettbw6mburfzfq/deluxe-room_standard.jpg?crop=107%2C0%2C1707%2C1280',
                ],
                'additional_gallery' => [
                    'https://image-tc.galaxy.tf/wijpeg-6nht1kgqd231644lqocr5vicg/deluxe-room_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-d4ibbjrvkpfettbw6mburfzfq/deluxe-room_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                ],
            ],
            [
                'id' => 39,
                'name' => 'Standard Twin',
                'slug' => 'deira-standard-twin',
                'short_description' => 'Unwind in our 33-square-meter Standard Twin Room, featuring two comfortable single beds. Perfectly designed for both relaxation and convenience.',
                'description' => '<p>Unwind in our 33-square-meter Standard Twin Room, featuring two comfortable single beds. Perfectly designed for both relaxation and convenience, this bright and spacious retreat offers a peaceful haven with modern amenities, ensuring a comfortable stay for you and your companion.</p>',
                'size_sqm' => 33,
                'bed_type' => 'Twin Beds',
                'travelclick_roomtype_id' => '535025',
                'starting_price' => 'FROM AED 332.50',
                'read_more_label' => 'READ MORE',
                'read_more_link' => '/coral-hotels-resorts-dubai-deira/rooms-suites/standard-twin',
                'book_now_label' => 'BOOK NOW',
                'book_now_link' => 'https://reservations.travelclick.com/115806?HotelId=115806&languageid=1&rooms=1&adults=1&roomtypeid=535025',
                'meta_title' => 'Standard Twin | Coral Dubai Deira Hotel',
                'meta_description' => 'Stay in our Standard Twin at Coral Dubai Deira Hotel. Book your hotel stay now!',
                'sort_order' => 2,
                'special_features' => [
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Air Condition'],
                    ['icon' => 'heroicon-o-tv', 'feature_name' => 'Flat-screen TV'],
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Free Toiletries'],
                    ['icon' => 'heroicon-o-wifi', 'feature_name' => 'Complimentary Wi-Fi'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'Safety Deposit Box'],
                    ['icon' => 'heroicon-o-gift', 'feature_name' => 'Minibar'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'In-room Safe'],
                    ['icon' => 'heroicon-o-bolt', 'feature_name' => 'Hairdryer'],
                    ['icon' => 'heroicon-o-wrench-screwdriver', 'feature_name' => 'Ironing Board'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Tea/Coffee Making Facilities'],
                    ['icon' => 'heroicon-o-tv', 'feature_name' => 'Satellite TV'],
                    ['icon' => 'heroicon-o-bell', 'feature_name' => 'Wake-up service'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Kettle'],
                ],
                'featured_images' => [
                    'https://image-tc.galaxy.tf/wijpeg-2n80oouoyj0loa57fejnbx01q/1_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-d4ibbjrvkpfettbw6mburfzfq/2_standard.jpg?crop=107%2C0%2C1707%2C1280',
                ],
                'additional_gallery' => [
                    'https://image-tc.galaxy.tf/wijpeg-2n80oouoyj0loa57fejnbx01q/1_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-d4ibbjrvkpfettbw6mburfzfq/2_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                ],
            ],
            [
                'id' => 40,
                'name' => 'Standard King with Balcony',
                'slug' => 'deira-standard-king-balcony',
                'short_description' => 'Enjoy a restful stay in our 33-square-meter Standard King room with a private balcony, offering city view.',
                'description' => '<p>Enjoy a restful stay in our 33-square-meter Standard King room with a private balcony, offering city view. Perfectly designed for relaxation, this room features a luxurious king-size bed and modern amenities.</p><p>Please note, for safety reasons, balcony access is unavailable when children are present.</p>',
                'size_sqm' => 33,
                'bed_type' => 'King Bed',
                'travelclick_roomtype_id' => '535026',
                'starting_price' => 'FROM AED 370.00',
                'read_more_label' => 'READ MORE',
                'read_more_link' => '/coral-hotels-resorts-dubai-deira/rooms-suites/standard-king-with-balcony',
                'book_now_label' => 'BOOK NOW',
                'book_now_link' => 'https://reservations.travelclick.com/115806?HotelId=115806&languageid=1&rooms=1&adults=1&roomtypeid=535026',
                'meta_title' => 'Standard King with Balcony | Coral Dubai Deira Hotel',
                'meta_description' => 'Stay in our Standard King with Balcony at Coral Dubai Deira Hotel. Book your hotel stay now!',
                'sort_order' => 3,
                'special_features' => [
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Air Condition'],
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Free Toiletries'],
                    ['icon' => 'heroicon-o-wifi', 'feature_name' => 'Complimentary Wi-Fi'],
                    ['icon' => 'heroicon-o-gift', 'feature_name' => 'Minibar'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'In-room Safe'],
                    ['icon' => 'heroicon-o-bolt', 'feature_name' => 'Hairdryer'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Tea/Coffee Making Facilities'],
                ],
                'featured_images' => [
                    'https://image-tc.galaxy.tf/wijpeg-6nht1kgqd231644lqocr5vicg/1_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-aohn6k7m7q5lotqk1skhzjn99/2_standard.jpg?crop=107%2C0%2C1707%2C1280',
                ],
                'additional_gallery' => [
                    'https://image-tc.galaxy.tf/wijpeg-6nht1kgqd231644lqocr5vicg/1_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-aohn6k7m7q5lotqk1skhzjn99/2_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                ],
            ],
            [
                'name' => 'Standard Twin with Balcony',
                'slug' => 'deira-standard-twin-balcony',
                'short_description' => 'Relax and unwind in our 33-square-meter Standard Twin room, featuring a private balcony with stunning city views.',
                'description' => '<p>Relax and unwind in our 33-square-meter Standard Twin room, featuring a private balcony with stunning city views. Designed for comfort and convenience, this room includes two comfortable single beds and modern amenities.</p><p>Please note, for safety reasons, balcony access is unavailable when children are present.</p>',
                'size_sqm' => 33,
                'bed_type' => 'Twin Beds',
                'travelclick_roomtype_id' => '535027',
                'starting_price' => 'FROM AED 370.00',
                'read_more_label' => 'READ MORE',
                'read_more_link' => '/coral-hotels-resorts-dubai-deira/rooms-suites/standard-twin-with-balcony',
                'book_now_label' => 'BOOK NOW',
                'book_now_link' => 'https://reservations.travelclick.com/115806?HotelId=115806&languageid=1&rooms=1&adults=1&roomtypeid=535027',
                'meta_title' => 'Standard Twin with Balcony | Coral Dubai Deira Hotel',
                'meta_description' => 'Stay in our Standard Twin with Balcony at Coral Dubai Deira Hotel. Book your hotel stay now!',
                'sort_order' => 4,
                'special_features' => [
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Air Condition'],
                    ['icon' => 'heroicon-o-tv', 'feature_name' => 'Flat-screen TV'],
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Free Toiletries'],
                    ['icon' => 'heroicon-o-wifi', 'feature_name' => 'Complimentary Wi-Fi'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'Safety Deposit Box'],
                    ['icon' => 'heroicon-o-gift', 'feature_name' => 'Minibar'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'In-room Safe'],
                    ['icon' => 'heroicon-o-bolt', 'feature_name' => 'Hairdryer'],
                    ['icon' => 'heroicon-o-wrench-screwdriver', 'feature_name' => 'Ironing Board'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Tea/Coffee Making Facilities'],
                    ['icon' => 'heroicon-o-tv', 'feature_name' => 'Satellite TV'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Kettle'],
                ],
                'featured_images' => [
                    'https://image-tc.galaxy.tf/wijpeg-2n80oouoyj0loa57fejnbx01q/1_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-d4ibbjrvkpfettbw6mburfzfq/2_standard.jpg?crop=107%2C0%2C1707%2C1280',
                ],
                'additional_gallery' => [
                    'https://image-tc.galaxy.tf/wijpeg-2n80oouoyj0loa57fejnbx01q/1_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-d4ibbjrvkpfettbw6mburfzfq/2_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                ],
            ],
            [
                'name' => 'Premium King',
                'slug' => 'deira-premium-king',
                'short_description' => 'Indulge in the ultimate comfort of our Premium King Room, featuring a luxurious king-sized bed and an array of upgraded amenities.',
                'description' => '<p>Indulge in the ultimate comfort of our Premium King Room, featuring a luxurious king-sized bed and an array of upgraded amenities. Designed to offer extra space and exceptional comfort, this room provides a perfect retreat for those seeking a higher level of relaxation and convenience.</p><p><strong>Additional amenities include:</strong></p><ul><li>High speed internet connection for up to 5 devices</li><li>Located on our higher floors (6th &amp; 7th)</li><li>2 pieces of complimentary regular laundry</li><li>Complimentary minibar</li><li>Special amenities like bathrobes in the room</li><li>Balcony (Front View subject to availability)</li></ul>',
                'size_sqm' => 33,
                'bed_type' => 'King Bed',
                'travelclick_roomtype_id' => '535028',
                'starting_price' => 'FROM AED 410.00',
                'read_more_label' => 'READ MORE',
                'read_more_link' => '/coral-hotels-resorts-dubai-deira/rooms-suites/premium-king',
                'book_now_label' => 'BOOK NOW',
                'book_now_link' => 'https://reservations.travelclick.com/115806?HotelId=115806&languageid=1&rooms=1&adults=1&roomtypeid=535028',
                'meta_title' => 'Premium King | Coral Dubai Deira Hotel',
                'meta_description' => 'Stay in our Premium King at Coral Dubai Deira Hotel. Book your hotel stay now!',
                'sort_order' => 5,
                'special_features' => [
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Air Condition'],
                    ['icon' => 'heroicon-o-wifi', 'feature_name' => 'Complimentary Wi-Fi'],
                    ['icon' => 'heroicon-o-bolt', 'feature_name' => 'Hairdryer'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Tea/Coffee Making Facilities'],
                    ['icon' => 'heroicon-o-gift', 'feature_name' => 'Complimentary Minibar'],
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'High Floor (6th & 7th)'],
                ],
                'featured_images' => [
                    'https://image-tc.galaxy.tf/wijpeg-1joav5ou26l82mxu3cxejoiij/club-room_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-zjjni6inmzpu5vscfx9b4503/club-room_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-1n8lhux0czxinvn81vg5rktuq/club-room_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-v1hvice6dcu7cptjsbnb76xb/club-room_standard.jpg?crop=107%2C0%2C1707%2C1280',
                ],
                'additional_gallery' => [
                    'https://image-tc.galaxy.tf/wijpeg-1joav5ou26l82mxu3cxejoiij/club-room_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-zjjni6inmzpu5vscfx9b4503/club-room_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-1n8lhux0czxinvn81vg5rktuq/club-room_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-v1hvice6dcu7cptjsbnb76xb/club-room_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                ],
            ],
            [
                'name' => 'Premium Twin',
                'slug' => 'deira-premium-twin',
                'short_description' => 'Experience elevated comfort and style in our Premium Twin Room at Coral Dubai Deira. Featuring two single beds and upgraded amenities.',
                'description' => '<p>Experience elevated comfort and style in our Premium Twin Room at Coral Dubai Deira. Featuring two single beds and upgraded amenities, this spacious room offers the perfect blend of relaxation and convenience.</p><p><strong>Additional amenities include:</strong></p><ul><li>High speed internet connection for up to 5 devices</li><li>Located on our higher floors (6th &amp; 7th)</li><li>2 pieces of complimentary regular laundry</li><li>Complimentary minibar</li><li>Special amenities like bathrobes in the room</li><li>Balcony (Front View subject to availability)</li></ul>',
                'size_sqm' => 33,
                'bed_type' => 'Twin Beds',
                'travelclick_roomtype_id' => '535029',
                'starting_price' => 'FROM AED 410.00',
                'read_more_label' => 'READ MORE',
                'read_more_link' => '/coral-hotels-resorts-dubai-deira/rooms-suites/premium-twin',
                'book_now_label' => 'BOOK NOW',
                'book_now_link' => 'https://reservations.travelclick.com/115806?HotelId=115806&languageid=1&rooms=1&adults=1&roomtypeid=535029',
                'meta_title' => 'Premium Twin | Coral Dubai Deira Hotel',
                'meta_description' => 'Stay in our Premium Twin at Coral Dubai Deira Hotel. Book your hotel stay now!',
                'sort_order' => 6,
                'special_features' => [
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Air Condition'],
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Free Toiletries'],
                    ['icon' => 'heroicon-o-wifi', 'feature_name' => 'Complimentary Wi-Fi'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'Safety Deposit Box'],
                    ['icon' => 'heroicon-o-gift', 'feature_name' => 'Minibar'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'In-room Safe'],
                    ['icon' => 'heroicon-o-bolt', 'feature_name' => 'Hairdryer'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Tea/Coffee Making Facilities'],
                ],
                'featured_images' => [
                    'https://image-tc.galaxy.tf/wijpeg-2n80oouoyj0loa57fejnbx01q/1_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-d4ibbjrvkpfettbw6mburfzfq/2_standard.jpg?crop=107%2C0%2C1707%2C1280',
                ],
                'additional_gallery' => [
                    'https://image-tc.galaxy.tf/wijpeg-2n80oouoyj0loa57fejnbx01q/1_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-d4ibbjrvkpfettbw6mburfzfq/2_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                ],
            ],
            [
                'id' => 41,
                'name' => 'Junior Suite',
                'slug' => 'deira-junior-suite',
                'short_description' => 'Discover ultimate comfort in our expansive Junior Suite, offering 56 square meters of open space and a luxurious king-size bed.',
                'description' => '<p>Discover ultimate comfort in our expansive Junior Suite, offering 56 square meters of open space and a luxurious king-size bed. Designed for executive travelers and extended stays, it features an integrated lounge seating area, modern en-suite bath, and premium guest amenities.</p>',
                'size_sqm' => 56,
                'bed_type' => 'King Bed',
                'travelclick_roomtype_id' => '535030',
                'starting_price' => 'FROM AED 490.00',
                'read_more_label' => 'READ MORE',
                'read_more_link' => '/coral-hotels-resorts-dubai-deira/rooms-suites/junior-suite',
                'book_now_label' => 'BOOK NOW',
                'book_now_link' => 'https://reservations.travelclick.com/115806?HotelId=115806&languageid=1&rooms=1&adults=1&roomtypeid=535030',
                'meta_title' => 'Junior Suite | Coral Dubai Deira Hotel',
                'meta_description' => 'Stay in our Junior Suite at Coral Dubai Deira Hotel. Book your hotel stay now!',
                'sort_order' => 7,
                'special_features' => [
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Air Condition'],
                    ['icon' => 'heroicon-o-home', 'feature_name' => 'Family Room'],
                    ['icon' => 'heroicon-o-wifi', 'feature_name' => 'Complimentary Wi-Fi'],
                    ['icon' => 'heroicon-o-bolt', 'feature_name' => 'Hairdryer'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Tea/Coffee Making Facilities'],
                ],
                'featured_images' => [
                    'https://image-tc.galaxy.tf/wijpeg-1zysbbdzfic50mjkgj5axdak9/junior-suite_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-7d9z1sz2z54gk07a98wczd2x9/junior-suite_standard.jpg?crop=97%2C0%2C1727%2C1295',
                ],
                'additional_gallery' => [
                    'https://image-tc.galaxy.tf/wijpeg-1zysbbdzfic50mjkgj5axdak9/junior-suite_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-7d9z1sz2z54gk07a98wczd2x9/junior-suite_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                ],
            ],
            [
                'id' => 42,
                'name' => 'Executive Suite',
                'slug' => 'deira-executive-suite',
                'short_description' => 'Experience the 66-square-meter Executive Suite at Coral Dubai Deira, featuring a spacious king-size bed and exclusive amenities.',
                'description' => '<p>Experience the 66-square-meter Executive Suite at Coral Dubai Deira. Featuring a spacious king-size bed and exclusive amenities, this suite offers the perfect blend of comfort, elegance, and convenience. Designed for both business and leisure travelers, it provides a refined setting for a truly exceptional stay with a separate living area, work desk, and luxury bathroom.</p>',
                'size_sqm' => 66,
                'bed_type' => 'King Bed',
                'travelclick_roomtype_id' => '535031',
                'starting_price' => 'FROM AED 590.00',
                'read_more_label' => 'READ MORE',
                'read_more_link' => '/coral-hotels-resorts-dubai-deira/rooms-suites/executive-suite',
                'book_now_label' => 'BOOK NOW',
                'book_now_link' => 'https://reservations.travelclick.com/115806?HotelId=115806&languageid=1&rooms=1&adults=1&roomtypeid=535031',
                'meta_title' => 'Executive Suite | Coral Dubai Deira Hotel',
                'meta_description' => 'Stay in our Executive Suite at Coral Dubai Deira Hotel. Book your hotel stay now!',
                'sort_order' => 8,
                'special_features' => [
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Air Condition'],
                    ['icon' => 'heroicon-o-tv', 'feature_name' => 'Flat-screen TV'],
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Free Toiletries'],
                    ['icon' => 'heroicon-o-wifi', 'feature_name' => 'Complimentary Wi-Fi'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'Safety Deposit Box'],
                    ['icon' => 'heroicon-o-gift', 'feature_name' => 'Minibar'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'In-room Safe'],
                    ['icon' => 'heroicon-o-bolt', 'feature_name' => 'Hairdryer'],
                    ['icon' => 'heroicon-o-wrench-screwdriver', 'feature_name' => 'Ironing Board'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Tea/Coffee Making Facilities'],
                    ['icon' => 'heroicon-o-tv', 'feature_name' => 'Satellite TV'],
                    ['icon' => 'heroicon-o-bell', 'feature_name' => 'Wake-up service'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Kettle'],
                ],
                'featured_images' => [
                    'https://image-tc.galaxy.tf/wijpeg-aldl864ybtmn3w41injw7ahtm/executive-suite_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-sohdi0txupjj6we4bzbpd3e7/executive-suite_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-aik2a104ol6yfrccwrq1znjuz/executive-suite_standard.jpg?crop=97%2C0%2C1727%2C1295',
                ],
                'additional_gallery' => [
                    'https://image-tc.galaxy.tf/wijpeg-aldl864ybtmn3w41injw7ahtm/executive-suite_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-sohdi0txupjj6we4bzbpd3e7/executive-suite_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-aik2a104ol6yfrccwrq1znjuz/executive-suite_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                ],
            ],
            [
                'id' => 43,
                'name' => 'Family Room',
                'slug' => 'deira-family-room',
                'short_description' => 'Enjoy a memorable family getaway at Coral Dubai Deira Hotel. Our spacious 66sqm Family Rooms are perfect for larger groups.',
                'description' => '<p>Enjoy a memorable family getaway at Coral Dubai Deira Hotel. Our spacious and thoughtfully designed Family Rooms are perfect for larger groups, offering the ideal balance of privacy and space for everyone to relax. Whether you\'re traveling with children or extended family, these rooms provide the comfort and convenience needed for a truly enjoyable stay.</p>',
                'size_sqm' => 66,
                'bed_type' => '1 King + 2 Twin Beds',
                'travelclick_roomtype_id' => '535032',
                'starting_price' => 'FROM AED 620.00',
                'read_more_label' => 'READ MORE',
                'read_more_link' => '/coral-hotels-resorts-dubai-deira/rooms-suites/family-room',
                'book_now_label' => 'BOOK NOW',
                'book_now_link' => 'https://reservations.travelclick.com/115806?HotelId=115806&languageid=1&rooms=1&adults=1&roomtypeid=535032',
                'meta_title' => 'Family Room | Coral Dubai Deira Hotel',
                'meta_description' => 'Stay in our Family Room at Coral Dubai Deira Hotel. Book your hotel stay now!',
                'sort_order' => 9,
                'special_features' => [
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Air Condition'],
                    ['icon' => 'heroicon-o-home', 'feature_name' => 'Family Room'],
                    ['icon' => 'heroicon-o-wifi', 'feature_name' => 'Complimentary Wi-Fi'],
                    ['icon' => 'heroicon-o-bolt', 'feature_name' => 'Hairdryer'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Tea/Coffee Making Facilities'],
                ],
                'featured_images' => [
                    'https://image-tc.galaxy.tf/wijpeg-2nhtvvlnpv9dhi2bzj1b5m9zf/family-room_standard.jpg?crop=175%2C0%2C1571%2C1178',
                    'https://image-tc.galaxy.tf/wijpeg-c6ouxs1mypmxjq9wm3g0cd3ha/family-room_standard.jpg?crop=79%2C0%2C1763%2C1322',
                    'https://image-tc.galaxy.tf/wijpeg-9v1ydqocnnc0ufxt5t6vbiks7/family-room_standard.jpg?crop=107%2C0%2C1707%2C1280',
                ],
                'additional_gallery' => [
                    'https://image-tc.galaxy.tf/wijpeg-2nhtvvlnpv9dhi2bzj1b5m9zf/family-room_wide.jpg?crop=0%2C49%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-c6ouxs1mypmxjq9wm3g0cd3ha/family-room_wide.jpg?crop=0%2C49%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-9v1ydqocnnc0ufxt5t6vbiks7/family-room_wide.jpg?crop=0%2C49%2C1920%2C1080&width=1920',
                ],
            ],
            [
                'name' => 'Family Room with King and Twin Component Room',
                'slug' => 'deira-family-room-component',
                'short_description' => 'Our spacious 66sqm Family Rooms are thoughtfully designed with interconnected King and Twin component rooms for larger groups.',
                'description' => '<p>Enjoy a memorable family getaway at Coral Dubai Deira Hotel. Our spacious 66sqm Family Rooms are thoughtfully designed for larger groups, offering the perfect balance of privacy and space for everyone to relax. Whether you\'re traveling with children or extended family, these rooms provide the comfort and convenience needed for a truly enjoyable stay.</p>',
                'size_sqm' => 66,
                'bed_type' => '1 King + 2 Twin Beds',
                'travelclick_roomtype_id' => '535033',
                'starting_price' => 'FROM AED 620.00',
                'read_more_label' => 'READ MORE',
                'read_more_link' => '/coral-hotels-resorts-dubai-deira/rooms-suites/family-room-with-king-and-twin-component-room',
                'book_now_label' => 'BOOK NOW',
                'book_now_link' => 'https://reservations.travelclick.com/115806?HotelId=115806&languageid=1&rooms=1&adults=1&roomtypeid=535033',
                'meta_title' => 'Family Room with King and Twin Component Room | Coral Dubai Deira Hotel',
                'meta_description' => 'Stay in our Family Room with King and Twin Component Room at Coral Dubai Deira Hotel. Book your hotel stay now!',
                'sort_order' => 10,
                'special_features' => [
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Air Condition'],
                    ['icon' => 'heroicon-o-sparkles', 'feature_name' => 'Free Toiletries'],
                    ['icon' => 'heroicon-o-wifi', 'feature_name' => 'Complimentary Wi-Fi'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'Safety Deposit Box'],
                    ['icon' => 'heroicon-o-lock-closed', 'feature_name' => 'In-room Safe'],
                    ['icon' => 'heroicon-o-bolt', 'feature_name' => 'Hairdryer'],
                    ['icon' => 'heroicon-o-cup-hot', 'feature_name' => 'Tea/Coffee Making Facilities'],
                ],
                'featured_images' => [
                    'https://image-tc.galaxy.tf/wijpeg-2nhtvvlnpv9dhi2bzj1b5m9zf/1_standard.jpg?crop=175%2C0%2C1571%2C1178',
                    'https://image-tc.galaxy.tf/wijpeg-2n80oouoyj0loa57fejnbx01q/1_standard.jpg?crop=107%2C0%2C1707%2C1280',
                    'https://image-tc.galaxy.tf/wijpeg-9v1ydqocnnc0ufxt5t6vbiks7/3_standard.jpg?crop=107%2C0%2C1707%2C1280',
                ],
                'additional_gallery' => [
                    'https://image-tc.galaxy.tf/wijpeg-2nhtvvlnpv9dhi2bzj1b5m9zf/1_wide.jpg?crop=0%2C49%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-2n80oouoyj0loa57fejnbx01q/1_wide.jpg?crop=0%2C100%2C1920%2C1080&width=1920',
                    'https://image-tc.galaxy.tf/wijpeg-9v1ydqocnnc0ufxt5t6vbiks7/3_wide.jpg?crop=0%2C49%2C1920%2C1080&width=1920',
                ],
            ],
        ];

        $streamContext = stream_context_create([
            'http' => ['header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"],
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
        ]);

        foreach ($rooms as $data) {
            $room = null;
            if (isset($data['id'])) {
                $room = RoomType::find($data['id']);
            }
            if (!$room) {
                $room = RoomType::where('property_id', 32)
                    ->where(function($q) use ($data) {
                        $q->where('slug', $data['slug'])
                          ->orWhere('name->en', $data['name']);
                    })->first();
            }

            $roomAttributes = [
                'property_id' => 32,
                'name' => ['en' => $data['name']],
                'slug' => $data['slug'],
                'short_description' => ['en' => $data['short_description']],
                'description' => ['en' => $data['description']],
                'size_sqm' => $data['size_sqm'],
                'bed_type' => $data['bed_type'],
                'travelclick_roomtype_id' => $data['travelclick_roomtype_id'],
                'starting_price' => $data['starting_price'],
                'read_more_label' => $data['read_more_label'],
                'read_more_link' => $data['read_more_link'],
                'book_now_label' => $data['book_now_label'],
                'book_now_link' => $data['book_now_link'],
                'special_features' => $data['special_features'],
                'is_active' => true,
                'sort_order' => $data['sort_order'],
            ];

            if ($room) {
                $room->update($roomAttributes);
                $this->command->info("Updated Room Type #{$room->id}: {$data['name']}");
            } else {
                $room = RoomType::create($roomAttributes);
                $this->command->info("Created Room Type #{$room->id}: {$data['name']}");
            }

            // Update SEO Metadata
            $room->seoMetadata()->updateOrCreate(
                [],
                [
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                ]
            );

            // Handle Featured Images
            $room->clearMediaCollection('featured_image');
            foreach ($data['featured_images'] as $idx => $imgUrl) {
                $ext = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $safeName = Str::slug($data['name']) . '_featured_' . ($idx + 1) . '.' . $ext;
                $localPath = $tempDir . '/' . $safeName;
                $content = @file_get_contents($imgUrl, false, $streamContext);
                if ($content) {
                    file_put_contents($localPath, $content);
                    $room->addMedia($localPath)
                        ->usingName(Str::slug($data['name']) . '_featured_' . ($idx + 1))
                        ->usingFileName($safeName)
                        ->toMediaCollection('featured_image', 'uploads');
                    @unlink($localPath);
                }
            }

            // Handle Additional Gallery Images
            $room->clearMediaCollection('additional_gallery');
            foreach ($data['additional_gallery'] as $idx => $imgUrl) {
                $ext = pathinfo(parse_url($imgUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $safeName = Str::slug($data['name']) . '_gallery_' . ($idx + 1) . '.' . $ext;
                $localPath = $tempDir . '/' . $safeName;
                $content = @file_get_contents($imgUrl, false, $streamContext);
                if ($content) {
                    file_put_contents($localPath, $content);
                    $room->addMedia($localPath)
                        ->usingName(Str::slug($data['name']) . '_gallery_' . ($idx + 1))
                        ->usingFileName($safeName)
                        ->toMediaCollection('additional_gallery', 'uploads');
                    @unlink($localPath);
                }
            }
        }

        // Clean up any other room types for property 32 that aren't in this list
        $activeRoomIds = RoomType::where('property_id', 32)->where('sort_order', '>=', 1)->where('sort_order', '<=', 10)->pluck('id')->toArray();
        $staleRooms = RoomType::where('property_id', 32)->whereNotIn('id', $activeRoomIds)->get();
        foreach ($staleRooms as $stale) {
            $stale->clearMediaCollection('featured_image');
            $stale->clearMediaCollection('additional_gallery');
            $stale->seoMetadata()?->delete();
            $stale->delete();
            $this->command->info("Deleted stale room #{$stale->id}");
        }

        @rmdir($tempDir);
        $this->command->info("Room Types Seeder completed successfully!");
    }
}
