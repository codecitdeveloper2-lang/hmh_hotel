<?php
$url = 'https://www.hmhhotelgroup.com/bahi-hotels-resorts-bahi-ajman-palace';
$html = file_get_contents($url);

preg_match_all('/https:\/\/image-tc\.galaxy\.tf\/[^\s"\'\>]+/', $html, $matches);
$urls = array_unique($matches[0]);

foreach ($urls as $u) {
    if (strpos($u, 'wijpeg') !== false || strpos($u, 'wiwebp') !== false) {
        echo $u . "\n";
    }
}
