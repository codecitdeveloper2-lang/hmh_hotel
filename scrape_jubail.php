<?php
$ch = curl_init('https://www.hmhhotelgroup.com/coral-hotels-resorts-jubail');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$html = curl_exec($ch);
curl_close($ch);

preg_match_all('/href=["\'](\/[^"\']+|https:\/\/www\.hmhhotelgroup\.com[^"\']+)["\']/i', $html, $matches);
$links = array_unique($matches[1] ?? []);
$jubailLinks = array_filter($links, function($l) {
    return str_contains($l, 'jubail') || str_contains($l, 'coral');
});
print_r($jubailLinks);
