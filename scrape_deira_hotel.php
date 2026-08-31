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
        'images' => [],
        'paragraphs' => []
    ];

    if (!$html) return $meta;

    if (preg_match('/<title>(.*?)<\/title>/i', $html, $m)) $meta['title'] = html_entity_decode(trim($m[1]));
    if (preg_match('/<meta[^>]*name=[\"\']description[\"\'][^>]*content=[\"\'](.*?)[\"\']/i', $html, $m)) $meta['description'] = html_entity_decode(trim($m[1]));

    preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/[^\"\'\s<>]+\.(jpg|jpeg|png|webp|svg)/i', $html, $imgMatches);
    $meta['images'] = array_values(array_unique($imgMatches[0] ?? []));

    preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $html, $pMatches);
    foreach ($pMatches[1] as $p) {
        $clean = trim(strip_tags($p));
        if (strlen($clean) > 25) {
            $meta['paragraphs'][] = $clean;
        }
    }

    return $meta;
}

$urls = [
    'home' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-dubai-deira',
    'rooms' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-dubai-deira/rooms-suites',
    'dining' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-dubai-deira/dining',
    'facilities' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-dubai-deira/facilities',
    'attractions' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-dubai-deira/local-attractions',
    'faq' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-dubai-deira/faq',
    'contact' => 'https://www.hmhhotelgroup.com/coral-hotels-resorts-dubai-deira/location-and-contact',
];

$results = [];
foreach ($urls as $key => $url) {
    $results[$key] = getPageMeta($url);
}

file_put_contents('deira_scraped_data.json', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Scraped " . count($results) . " pages for Coral Dubai Deira.\n";
