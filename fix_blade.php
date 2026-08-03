<?php
$files = glob('resources/views/filament/pages/manage-*.blade.php');
$count = 0;
foreach ($files as $file) {
    $c = file_get_contents($file);
    
    // Replace View
    $c = preg_replace_callback(
        '/wire:click=[\'"]mount:?Action\([\'"]view([a-zA-Z0-9_]+)[\'"],\s*\{\s*id:\s*\{\{\s*\$([a-zA-Z0-9_]+)\[[\'"]id[\'"]\]\s*\}\}\s*\}\)[\'"]/',
        function($m) {
            $moduleName = $m[1];
            if ($moduleName === 'NewsAndPress') $plural = 'NewsAndPress';
            elseif ($moduleName === 'GroupGallery') $plural = 'GroupGallery';
            elseif ($moduleName === 'DiningOutlet') $plural = 'DiningOutlets';
            elseif ($moduleName === 'RoomType') $plural = 'RoomTypes';
            else $plural = $moduleName . 's'; // Default to plural
            
            // Special cases
            if ($moduleName === 'Faq') $plural = 'Faqs';
            if ($moduleName === 'MeetingsAndEvent') $plural = 'MeetingsAndEvents';
            
            // Plural for module folder:
            // Let's just find the exact module folder from app/Filament/Pages/
            $dirs = glob('app/Filament/Pages/*', GLOB_ONLYDIR);
            $folder = '';
            foreach ($dirs as $dir) {
                if (stripos(basename($dir), $moduleName) !== false) {
                    $folder = basename($dir);
                    break;
                }
            }
            if (!$folder) $folder = $plural;

            return 'tag="a" href="{{ \\App\\Filament\\Pages\\' . $folder . '\\View' . $moduleName . '::getUrl([\'record\' => $' . $m[2] . '[\'id\']]) }}"';
        },
        $c
    );

    // Replace Edit
    $c = preg_replace_callback(
        '/wire:click=[\'"]mount:?Action\([\'"]edit([a-zA-Z0-9_]+)[\'"],\s*\{\s*id:\s*\{\{\s*\$([a-zA-Z0-9_]+)\[[\'"]id[\'"]\]\s*\}\}\s*\}\)[\'"]/',
        function($m) {
            $moduleName = $m[1];
            
            // Find folder
            $dirs = glob('app/Filament/Pages/*', GLOB_ONLYDIR);
            $folder = '';
            foreach ($dirs as $dir) {
                if (stripos(basename($dir), $moduleName) !== false) {
                    $folder = basename($dir);
                    break;
                }
            }
            if (!$folder) $folder = $moduleName . 's';

            return 'tag="a" href="{{ \\App\\Filament\\Pages\\' . $folder . '\\Edit' . $moduleName . '::getUrl([\'record\' => $' . $m[2] . '[\'id\']]) }}"';
        },
        $c
    );

    file_put_contents($file, $c);
    $count++;
}
echo "Updated $count blade files.";
