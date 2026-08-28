<?php

function getSpecificImages($html) {
    // Look for image-tc.galaxy.tf urls that are not favicon or generic logos
    preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/([^\s"\'<>]+\.(jpg|jpeg|png|webp))/i', $html, $matches);
    $imgs = [];
    foreach (array_unique($matches[0]) as $img) {
        if (!str_contains($img, 'favicon') && !str_contains($img, 'logo') && !str_contains($img, 'tripadvisor') && !str_contains($img, 'icon')) {
            $imgs[] = $img;
        }
    }
    return array_values($imgs);
}

// 1. HOME & CONTACT
echo "=== HOME & CONTACT ===\n";
$home = file_get_contents('scraped_jubail/home.html');
$contact = file_get_contents('scraped_jubail/location_contact.html');

preg_match('/<title>(.*?)<\/title>/is', $home, $mTitle);
preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $home, $mDesc);
echo "Title: " . trim($mTitle[1] ?? '') . "\n";
echo "Desc: " . trim($mDesc[1] ?? '') . "\n";

// Let's find contact info from $contact
preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $contact, $cP);
foreach ($cP[1] as $p) {
    $c = trim(strip_tags($p));
    if (str_contains($c, 'Jubail') || str_contains($c, 'Street') || str_contains($c, '+966') || str_contains($c, '@')) {
        echo "Contact line: $c\n";
    }
}

// Home paragraphs (intro)
preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $home, $hP);
echo "\n--- Home Paragraphs ---\n";
foreach ($hP[1] as $p) {
    $c = trim(strip_tags($p));
    if (strlen($c) > 30 && !str_contains($c, 'Cookies')) {
        echo "- $c\n\n";
    }
}

// 2. ROOMS
echo "\n=== ROOMS DETAILS ===\n";
foreach (['room_standard', 'room_deluxe', 'room_exec', 'room_coral', 'room_grand'] as $r) {
    $rHtml = file_get_contents("scraped_jubail/{$r}.html");
    preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $rHtml, $h1);
    preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $rHtml, $rP);
    $imgs = getSpecificImages($rHtml);
    echo "ROOM: " . trim(strip_tags($h1[1] ?? '')) . "\n";
    foreach ($rP[1] as $p) {
        $c = trim(strip_tags($p));
        if (strlen($c) > 30 && !str_contains($c, 'Cookies')) {
            echo "  Text: $c\n";
        }
    }
    echo "  Images: " . implode("\n    ", array_slice($imgs, 0, 4)) . "\n\n";
}

// 3. DINING
echo "\n=== DINING DETAILS ===\n";
foreach (['dining_nafoora', 'dining_rumours'] as $d) {
    $dHtml = file_get_contents("scraped_jubail/{$d}.html");
    preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $dHtml, $h1);
    preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $dHtml, $dP);
    $imgs = getSpecificImages($dHtml);
    echo "DINING: " . trim(strip_tags($h1[1] ?? '')) . "\n";
    foreach ($dP[1] as $p) {
        $c = trim(strip_tags($p));
        if (strlen($c) > 30 && !str_contains($c, 'Cookies')) {
            echo "  Text: $c\n";
        }
    }
    echo "  Images: " . implode("\n    ", array_slice($imgs, 0, 4)) . "\n\n";
}

// 4. FACILITIES
echo "\n=== FACILITIES ===\n";
$facHtml = file_get_contents('scraped_jubail/facilities.html');
preg_match_all('/<h[2-4][^>]*>(.*?)<\/h[2-4]>/is', $facHtml, $fH);
preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $facHtml, $fP);
$fImgs = getSpecificImages($facHtml);
print_r($fH[1] ?? []);
foreach ($fP[1] as $p) {
    $c = trim(strip_tags($p));
    if (strlen($c) > 20 && !str_contains($c, 'Cookies')) echo "  Fac Text: $c\n";
}
echo "  Fac Images: " . implode("\n    ", $fImgs) . "\n";

// 5. EXPLORE
echo "\n=== EXPLORE ===\n";
foreach (['explore_corniche', 'explore_ithra', 'explore_history'] as $e) {
    $eHtml = file_get_contents("scraped_jubail/{$e}.html");
    preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $eHtml, $h1);
    preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $eHtml, $eP);
    $imgs = getSpecificImages($eHtml);
    echo "ATTRACTION: " . trim(strip_tags($h1[1] ?? '')) . "\n";
    foreach ($eP[1] as $p) {
        $c = trim(strip_tags($p));
        if (strlen($c) > 30 && !str_contains($c, 'Cookies')) {
            echo "  Text: $c\n";
        }
    }
    echo "  Images: " . implode("\n    ", array_slice($imgs, 0, 3)) . "\n\n";
}

// 6. FAQ
echo "\n=== FAQs ===\n";
$faqHtml = file_get_contents('scraped_jubail/faq.html');
preg_match_all('/<h[2-4][^>]*>(.*?)<\/h[2-4]>/is', $faqHtml, $faqQ);
preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $faqHtml, $faqP);
print_r($faqQ[1] ?? []);
foreach ($faqP[1] as $p) {
    $c = trim(strip_tags($p));
    if (strlen($c) > 20 && !str_contains($c, 'Cookies')) echo "  FAQ: $c\n";
}

