<?php
$html = file_get_contents('C:\Users\soura\.gemini\antigravity-ide\brain\f744b14f-9579-4082-88fe-eb1d956867b9\.system_generated\steps\403\content.md');
preg_match_all('/href=\"([^\"]+)\"/', $html, $matches);
$links = array_unique($matches[1]);
foreach($links as $l) {
    if (strpos($l, 'coral') !== false) {
        echo $l . "\n";
    }
}
