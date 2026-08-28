<?php

function fetchUrl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'html' => $html];
}

$urls = [
    'home' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail',
    'location_contact' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/location-and-contact',
    'gallery' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/gallery',
    'rooms_index' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/rooms-suites',
    'room_standard' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/rooms-suites/standard-rooms',
    'room_deluxe' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/rooms-suites/deluxe-room',
    'room_exec' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/rooms-suites/executive-suite',
    'room_coral' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/rooms-suites/coral-suites',
    'room_grand' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/rooms-suites/grand-suite',
    'dining_index' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/dining',
    'dining_nafoora' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/dining/al-nafoora-jubail',
    'dining_rumours' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/dining/rumours-cafe-jubail',
    'facilities' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/facilities',
    'explore_index' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/explore-jubail',
    'explore_corniche' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/explore-jubail/al-fanateer-corniche',
    'explore_ithra' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/explore-jubail/king-abdulaziz-center-for-world-culture',
    'explore_history' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/explore-jubail/jubail-a-city-with-many-names',
    'faq' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail/frequently-asked-questions',
];

$data = [];
foreach ($urls as $key => $url) {
    echo "Fetching $key: $url ... ";
    $res = fetchUrl($url);
    echo "HTTP {$res['code']}, length " . strlen($res['html']) . "\n";
    $html = $res['html'];
    
    // Extract title
    $title = '';
    if (preg_match('/<title>(.*?)<\/title>/is', $html, $m)) {
        $title = html_entity_decode(trim($m[1]));
    }
    
    // Extract meta description
    $desc = '';
    if (preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $html, $m)) {
        $desc = html_entity_decode(trim($m[1]));
    }
    
    // Extract images
    preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/[^"\'\s<>]+\.(jpg|jpeg|png|webp|svg)/i', $html, $imgMatches);
    $images = array_values(array_unique($imgMatches[0] ?? []));
    
    // Extract headings
    preg_match_all('/<h[1-4][^>]*>(.*?)<\/h[1-4]>/is', $html, $hMatches);
    $headings = array_map(function($h) { return trim(strip_tags($h)); }, $hMatches[1] ?? []);
    
    // Extract paragraphs
    preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $pMatches);
    $paragraphs = [];
    foreach ($pMatches[1] as $p) {
        $clean = trim(strip_tags($p));
        if (strlen($clean) > 20 && !str_contains($clean, 'Cookies') && !str_contains($clean, 'JavaScript')) {
            $paragraphs[] = $clean;
        }
    }
    
    $data[$key] = [
        'url' => $url,
        'title' => $title,
        'meta_description' => $desc,
        'headings' => array_values(array_filter($headings)),
        'paragraphs' => array_values(array_unique($paragraphs)),
        'images' => $images,
        'raw_html_len' => strlen($html)
    ];
    
    // Save raw HTML for deep inspection
    if (!is_dir('scraped_jubail')) mkdir('scraped_jubail', 0777, true);
    file_put_contents("scraped_jubail/{$key}.html", $html);
}

file_put_contents('jubail_scraped_summary.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "\nAll Jubail pages scraped and saved to jubail_scraped_summary.json!\n";
