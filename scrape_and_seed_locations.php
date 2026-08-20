<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OurLocation;
use Illuminate\Support\Str;

$html = file_get_contents('hmh_home.html');
$doc = new DOMDocument();
@$doc->loadHTML($html);
$xpath = new DOMXPath($doc);

$nodes = $xpath->query('//h2[contains(text(), "Our Locations")]');
if ($nodes->length > 0) {
    $h2 = $nodes->item(0);
    $section = $h2->parentNode->parentNode->parentNode->parentNode;
    $slides = $xpath->query('.//*[contains(@class, "swiper-slide")]', $section);
    
    $order = 1;
    foreach ($slides as $slide) {
        $titleNode = $xpath->query('.//h3[contains(@class, "card-title")]', $slide)->item(0);
        $imgNode = $xpath->query('.//div[contains(@class, "card-image")]', $slide)->item(0);
        $descNode = $xpath->query('.//div[contains(@class, "card-desc")]', $slide)->item(0);
        
        if ($titleNode && $imgNode) {
            $city = trim($titleNode->textContent);
            $bgUrl = $imgNode->getAttribute('data-bg');
            $teaser = $descNode ? trim(preg_replace('/\s+/', ' ', $descNode->textContent)) : '';
            
            // Download image
            $filename = '';
            if ($bgUrl) {
                $ext = pathinfo(parse_url($bgUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                if (!$ext) $ext = 'jpg';
                $filename = 'location_' . Str::slug($city) . '.' . $ext;
                $filepath = public_path('uploads/' . $filename);
                if (!file_exists($filepath)) {
                    $imgData = @file_get_contents($bgUrl);
                    if ($imgData) {
                        file_put_contents($filepath, $imgData);
                    }
                }
            }
            
            OurLocation::updateOrCreate(
                ['city_name' => $city],
                [
                    'home_teaser' => $teaser,
                    'home_image' => $filename,
                    'featured_on_home' => true,
                    'display_order' => $order++
                ]
            );
            
            echo "Saved: $city\n";
        }
    }
}
