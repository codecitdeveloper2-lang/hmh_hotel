<?php
$html = file_get_contents('C:\Users\soura\.gemini\antigravity-ide\brain\f744b14f-9579-4082-88fe-eb1d956867b9\.system_generated\steps\496\content.md');
preg_match_all('/<h[123][^>]*>(.*?)<\/h[123]>/is', $html, $matches);
$titles = array_unique(array_map('strip_tags', $matches[1]));
print_r($titles);
