<?php

function fetchHtml($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

$roomsHtml = fetchHtml('https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/rooms');
file_put_contents('coral_rooms.html', $roomsHtml);

$diningHtml = fetchHtml('https://www.hmhhotelgroup.com/coral-hotels-resorts-beach-sharjah/dining');
file_put_contents('coral_dining.html', $diningHtml);

echo "HTML downloaded.\n";
