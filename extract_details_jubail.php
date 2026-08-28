<?php

// Function to clean text
function clean($t) {
    return trim(html_entity_decode(strip_tags($t)));
}

// 1. Gallery images
$galleryHtml = file_get_contents('scraped_jubail/gallery.html');
preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/([^\s"\'<>]+\.(jpg|jpeg|png|webp))/i', $galleryHtml, $gM);
$galleryImages = [];
foreach (array_unique($gM[0]) as $img) {
    if (!str_contains($img, 'favicon') && !str_contains($img, 'logo') && !str_contains($img, 'icon') && !str_contains($img, 'tripadvisor')) {
        $galleryImages[] = $img;
    }
}
echo "=== GALLERY IMAGES (" . count($galleryImages) . ") ===\n";
print_r($galleryImages);

// 2. Extract FAQ pairs
$faqHtml = file_get_contents('scraped_jubail/faq.html');
preg_match_all('/<div[^>]*class="[^"]*accordion[^"]*"[^>]*>(.*?)<\/div>/is', $faqHtml, $accM);
// or check for question/answer patterns
preg_match_all('/<button[^>]*>(.*?)<\/button>/is', $faqHtml, $btnM);
echo "\n=== FAQ BUTTONS/QUESTIONS ===\n";
foreach ($btnM[1] as $q) {
    $qClean = clean($q);
    if (strlen($qClean) > 5 && !str_contains($qClean, 'Book') && !str_contains($qClean, 'Menu')) {
        echo "Q: $qClean\n";
    }
}

// 3. Extract Dining details
echo "\n=== DINING PAGES ===\n";
foreach (['dining_nafoora', 'dining_rumours'] as $d) {
    $h = file_get_contents("scraped_jubail/{$d}.html");
    preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $h, $h1);
    preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/([^\s"\'<>]+\.(jpg|jpeg|png|webp))/i', $h, $imgs);
    $dImgs = array_filter(array_unique($imgs[0]), fn($i) => !str_contains($i, 'favicon') && !str_contains($i, 'logo'));
    echo "Title: " . clean($h1[1] ?? '') . "\n";
    echo "Images: " . implode(', ', $dImgs) . "\n";
}

// 4. Extract Room details
echo "\n=== ROOMS PAGES ===\n";
foreach (['room_standard', 'room_deluxe', 'room_exec', 'room_coral', 'room_grand'] as $r) {
    $h = file_get_contents("scraped_jubail/{$r}.html");
    preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $h, $h1);
    preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/([^\s"\'<>]+\.(jpg|jpeg|png|webp))/i', $h, $imgs);
    $rImgs = array_filter(array_unique($imgs[0]), fn($i) => !str_contains($i, 'favicon') && !str_contains($i, 'logo'));
    echo "Title: " . clean($h1[1] ?? '') . "\n";
    echo "Images: " . implode(', ', $rImgs) . "\n";
}

// 5. Extract Facilities
echo "\n=== FACILITIES ===\n";
$facHtml = file_get_contents('scraped_jubail/facilities.html');
preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/([^\s"\'<>]+\.(jpg|jpeg|png|webp))/i', $facHtml, $imgs);
$fImgs = array_filter(array_unique($imgs[0]), fn($i) => !str_contains($i, 'favicon') && !str_contains($i, 'logo'));
print_r(array_values($fImgs));

