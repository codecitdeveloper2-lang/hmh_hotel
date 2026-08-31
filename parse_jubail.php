<?php

$homeHtml = file_get_contents('scraped_jubail/home.html');
$locationHtml = file_get_contents('scraped_jubail/location_contact.html');
$roomsHtml = file_get_contents('scraped_jubail/rooms_index.html');
$diningHtml = file_get_contents('scraped_jubail/dining_index.html');
$facilitiesHtml = file_get_contents('scraped_jubail/facilities.html');
$exploreHtml = file_get_contents('scraped_jubail/explore_index.html');
$faqHtml = file_get_contents('scraped_jubail/faq.html');

echo "=== 1. HOTEL GENERAL / CONTACT ===\n";
// Address, phone, email, lat/lng
preg_match('/<address[^>]*>(.*?)<\/address>/is', $locationHtml ?: $homeHtml, $addrM);
echo "Address: " . strip_tags($addrM[1] ?? 'N/A') . "\n";

preg_match('/tel:([^\s"\']+)/i', $locationHtml . $homeHtml, $telM);
echo "Phone: " . ($telM[1] ?? 'N/A') . "\n";

preg_match('/mailto:([^\s"\']+)/i', $locationHtml . $homeHtml, $mailM);
echo "Email: " . ($mailM[1] ?? 'N/A') . "\n";

preg_match('/(2[6-7]\.\d+)[,\s]+(4[9-0]\.\d+|5[0-1]\.\d+)/', $locationHtml . $homeHtml, $coordM);
echo "Coords: " . ($coordM[0] ?? 'N/A') . "\n";

// Banners
preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/[^"\'\s<>]+\.(jpg|jpeg|png|webp)/i', $homeHtml, $homeImgs);
$bannerImgs = array_values(array_unique($homeImgs[0]));
echo "Home images found: " . count($bannerImgs) . "\n";
print_r(array_slice($bannerImgs, 0, 10));

// Rooms
echo "\n=== 2. ROOMS ===\n";
foreach (['room_standard', 'room_deluxe', 'room_exec', 'room_coral', 'room_grand'] as $rFile) {
    $rHtml = file_get_contents("scraped_jubail/{$rFile}.html");
    preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $rHtml, $h1);
    preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $rHtml, $desc);
    preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/[^"\'\s<>]+\.(jpg|jpeg|png|webp)/i', $rHtml, $rImgs);
    echo "Room File: $rFile | H1: " . trim(strip_tags($h1[1] ?? '')) . " | Desc: " . trim($desc[1] ?? '') . "\n";
    echo "  Images: " . implode(', ', array_slice(array_unique($rImgs[0]), 0, 3)) . "\n";
}

// Dining
echo "\n=== 3. DINING OUTLETS ===\n";
foreach (['dining_nafoora', 'dining_rumours'] as $dFile) {
    $dHtml = file_get_contents("scraped_jubail/{$dFile}.html");
    preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $dHtml, $h1);
    preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $dHtml, $desc);
    preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/[^"\'\s<>]+\.(jpg|jpeg|png|webp)/i', $dHtml, $dImgs);
    echo "Dining File: $dFile | H1: " . trim(strip_tags($h1[1] ?? '')) . " | Desc: " . trim($desc[1] ?? '') . "\n";
    echo "  Images: " . implode(', ', array_slice(array_unique($dImgs[0]), 0, 3)) . "\n";
}

// Explore Jubail
echo "\n=== 4. EXPLORE / ATTRACTIONS ===\n";
foreach (['explore_corniche', 'explore_ithra', 'explore_history'] as $eFile) {
    $eHtml = file_get_contents("scraped_jubail/{$eFile}.html");
    preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $eHtml, $h1);
    preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $eHtml, $desc);
    preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/[^"\'\s<>]+\.(jpg|jpeg|png|webp)/i', $eHtml, $eImgs);
    echo "Explore File: $eFile | H1: " . trim(strip_tags($h1[1] ?? '')) . " | Desc: " . trim($desc[1] ?? '') . "\n";
    echo "  Images: " . implode(', ', array_slice(array_unique($eImgs[0]), 0, 3)) . "\n";
}

