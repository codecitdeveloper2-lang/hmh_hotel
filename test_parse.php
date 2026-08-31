<?php
$html = file_get_contents('coral_rooms.html');
$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

$rooms = [];
$nodes = $xpath->query('//div[contains(@class, "room-item") or contains(@class, "card")] | //article');
if ($nodes->length === 0) {
    // try finding h2s or h3s
    $headers = $xpath->query('//h2 | //h3');
    foreach($headers as $h) {
        echo "Header: " . trim($h->textContent) . "\n";
    }
}

// Let's just output the h2 and h3
echo "H2s:\n";
foreach($xpath->query('//h2') as $n) echo trim($n->textContent)."\n";
echo "H3s:\n";
foreach($xpath->query('//h3') as $n) echo trim($n->textContent)."\n";
