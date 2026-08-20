<?php
$html = file_get_contents('https://www.hmhhotelgroup.com/');
file_put_contents('hmh_home.html', $html);
echo "Fetched HTML. Size: " . strlen($html) . "\n";
