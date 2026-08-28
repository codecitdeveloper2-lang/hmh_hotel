<?php
function clean($t) {
    return trim(html_entity_decode(strip_tags($t)));
}

echo "=== EXPLORE DETAILS ===\n";
foreach (['explore_corniche', 'explore_ithra', 'explore_history'] as $e) {
    $h = file_get_contents("scraped_jubail/{$e}.html");
    preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $h, $h1);
    preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $h, $desc);
    preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/([^\s"\'<>]+\.(jpg|jpeg|png|webp))/i', $h, $imgs);
    $eImgs = array_filter(array_unique($imgs[0]), fn($i) => !str_contains($i, 'favicon') && !str_contains($i, 'logo') && !str_contains($i, 'coral-jubail-hotel'));
    echo "Title: " . clean($h1[1] ?? '') . "\n";
    echo "Meta Desc: " . clean($desc[1] ?? '') . "\n";
    preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $h, $p);
    foreach ($p[1] as $pText) {
        $c = clean($pText);
        if (strlen($c) > 30 && !str_contains($c, 'Cookies')) echo "  Paragraph: $c\n";
    }
    echo "  Images: " . implode(', ', $eImgs) . "\n\n";
}

echo "=== ROOM SPECIFICATIONS ===\n";
foreach (['room_standard', 'room_deluxe', 'room_exec', 'room_coral', 'room_grand'] as $r) {
    $h = file_get_contents("scraped_jubail/{$r}.html");
    preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $h, $h1);
    echo "=== Room: " . clean($h1[1] ?? '') . " ===\n";
    preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $h, $lis);
    foreach ($lis[1] as $li) {
        $c = clean($li);
        if (strlen($c) > 3 && !str_contains($c, 'Menu') && !str_contains($c, 'Book') && strlen($c) < 100) {
            echo "  - $c\n";
        }
    }
}
