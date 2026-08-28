<?php

foreach (['room_standard', 'room_deluxe', 'room_exec', 'room_coral', 'room_grand'] as $r) {
    $h = file_get_contents("scraped_jubail/{$r}.html");
    echo "=== FILE: $r ===\n";
    // Find all text inside main or content divs
    if (preg_match('/<main[^>]*>(.*?)<\/main>/is', $h, $m)) {
        $content = $m[1];
    } else {
        $content = $h;
    }
    preg_match_all('/<(?:p|h1|h2|h3)[^>]*>(.*?)<\/(?:p|h1|h2|h3)>/is', $content, $tags);
    foreach ($tags[1] as $t) {
        $clean = trim(html_entity_decode(strip_tags($t)));
        if (strlen($clean) > 20 && !str_contains($clean, 'Cookies') && !str_contains($clean, 'HMH') && !str_contains($clean, 'Terms') && !str_contains($clean, 'Book a room')) {
            echo "  $clean\n";
        }
    }
}
