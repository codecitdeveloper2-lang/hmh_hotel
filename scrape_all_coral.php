<?php
function getPageMeta($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);
    curl_close($ch);

    $meta = [
        'url' => $url,
        'title' => '',
        'description' => '',
        'og_image' => '',
        'images' => [],
        'headings' => [],
        'paragraphs' => []
    ];

    if (!$html) return $meta;

    if (preg_match('/<title>(.*?)<\/title>/i', $html, $m)) $meta['title'] = html_entity_decode(trim($m[1]));
    if (preg_match('/<meta[^>]*name=[\"\']description[\"\'][^>]*content=[\"\'](.*?)[\"\']/i', $html, $m)) $meta['description'] = html_entity_decode(trim($m[1]));
    if (preg_match('/<meta[^>]*property=[\"\']og:image[\"\'][^>]*content=[\"\'](.*?)[\"\']/i', $html, $m)) $meta['og_image'] = trim($m[1]);

    preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/[^\"\'\s<>]+\.(jpg|jpeg|png|webp|svg)/i', $html, $imgMatches);
    $meta['images'] = array_values(array_unique($imgMatches[0] ?? []));

    // Extract all paragraphs with content
    preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $pMatches);
    foreach ($pMatches[1] as $p) {
        $clean = trim(strip_tags($p));
        if (strlen($clean) > 30) {
            $meta['paragraphs'][] = $clean;
        }
    }

    return $meta;
}

$urls = [
    'home' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah',
    'rooms' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/rooms-suites',
    'room_standard_king' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/rooms-suites/standard-city-view-room',
    'room_standard_twin' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/rooms-suites/standard-twin',
    'room_standard_sea' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/rooms-suites/standard-sea-view-room',
    'room_standard_twin_view' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/rooms-suites/standard-twin-room-with-a-view',
    'room_family_city' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/rooms-suites/family-room-city-view',
    'room_family_sea' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/rooms-suites/family-room-sea-view',
    'dining' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/dining',
    'dining_al_dente' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/dining/al-dente',
    'dining_cote_jardin' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/dining/cote-jardin',
    'dining_casa_samak' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/dining/casa-samak-seafood-restaurant',
    'dining_rumours' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/dining/rumours-cafe',
    'dining_waves' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/dining/waves',
    'attractions' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/local-attractions',
    'faq' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/faq',
];

$results = [];
foreach ($urls as $key => $url) {
    $results[$key] = getPageMeta($url);
}

file_put_contents('coral_scraped_data.json', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Scraped " . count($results) . " pages successfully.\n";
