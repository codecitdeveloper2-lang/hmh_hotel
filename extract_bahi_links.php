<?php
$url = 'https://www.hmhhotelgroup.com/bahi-hotels-resorts-bahi-ajman-palace';
$html = file_get_contents($url);

preg_match_all('/href=\"([^\"]+)\"/', $html, $matches);
$links = array_unique($matches[1]);
foreach($links as $link) {
    if (strpos(strtolower($link), 'room') !== false || strpos(strtolower($link), 'accommodation') !== false || strpos(strtolower($link), 'stay') !== false) {
        echo $link . "\n";
    }
}
