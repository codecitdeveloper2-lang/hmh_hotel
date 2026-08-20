<?php
$html = file_get_contents('hmh_home.html');
$doc = new DOMDocument();
@$doc->loadHTML($html);
$xpath = new DOMXPath($doc);

$nodes = $xpath->query('//h2[contains(text(), "Our Locations")]');
if ($nodes->length > 0) {
    $h2 = $nodes->item(0);
    $section = $h2->parentNode->parentNode->parentNode->parentNode;
    $slides = $xpath->query('.//*[contains(@class, "swiper-slide")]', $section);
    
    if ($slides->length > 0) {
        $slide = $slides->item(0);
        $htmlStr = $doc->saveHTML($slide);
        echo $htmlStr;
    }
}
