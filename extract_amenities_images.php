<?php
$html = file_get_contents('https://www.hmhhotelgroup.com/bahi-hotels-resorts-bahi-ajman-palace/facilities');
$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

$facilities = [];

$headings = $xpath->query('//h3');

foreach ($headings as $h3) {
    $title = trim($h3->textContent);
    if (empty($title)) continue;

    $parent = $h3->parentNode;
    $imgSrc = null;
    $depth = 0;
    while ($parent && $depth < 5) {
        $imgs = $xpath->query('.//img | .//source', $parent);
        if ($imgs->length > 0) {
            foreach ($imgs as $img) {
                $src = $img->getAttribute('data-src') ?: $img->getAttribute('srcset') ?: $img->getAttribute('src');
                if (strpos($src, 'data:image') !== 0 && !empty($src)) {
                    // srcset might contain multiple, take the first one
                    $src = explode(',', $src)[0];
                    $src = explode(' ', trim($src))[0];
                    if (strpos($src, 'http') !== 0) {
                        $src = 'https://www.hmhhotelgroup.com' . $src;
                    }
                    $imgSrc = $src;
                    break 2;
                }
            }
        }
        
        // try looking for background image in style
        $divs = $xpath->query('.//*[@style]', $parent);
        foreach ($divs as $div) {
            $style = $div->getAttribute('style');
            if (preg_match('/url\([\'"]?(.*?)[\'"]?\)/', $style, $matches)) {
                $src = $matches[1];
                if (strpos($src, 'http') !== 0) {
                    $src = 'https://www.hmhhotelgroup.com' . $src;
                }
                $imgSrc = $src;
                break 2;
            }
        }
        
        $parent = $parent->parentNode;
        $depth++;
    }
    
    // If no img found in the immediate wrapper, try broader sibling search
    if (!$imgSrc) {
        $parent = $h3->parentNode->parentNode;
        if ($parent) {
             $imgs = $xpath->query('.//img | .//source', $parent);
             foreach ($imgs as $img) {
                $src = $img->getAttribute('data-src') ?: $img->getAttribute('srcset') ?: $img->getAttribute('src');
                if (strpos($src, 'data:image') !== 0 && !empty($src)) {
                    $src = explode(',', $src)[0];
                    $src = explode(' ', trim($src))[0];
                    if (strpos($src, 'http') !== 0) {
                        $src = 'https://www.hmhhotelgroup.com' . $src;
                    }
                    $imgSrc = $src;
                    break;
                }
             }
        }
    }
    
    $facilities[] = [
        'title' => $title,
        'image' => $imgSrc
    ];
}

print_r($facilities);
