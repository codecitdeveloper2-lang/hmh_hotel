<?php
$files = glob('app/Filament/Pages/Manage*.php');
$count = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    $basename = basename($file, '.php');
    $module = str_replace('Manage', '', $basename);
    
    // Expected class names
    $singular = rtrim($module, 's');
    if ($module === 'NewsAndPress') $singular = 'NewsAndPress';
    if ($module === 'GroupGallery') $singular = 'GroupGallery';
    if ($module === 'DiningOutlets') $singular = 'DiningOutlet';
    if ($module === 'RoomTypes') $singular = 'RoomType';
    if ($module === 'Faqs') $singular = 'Faq';
    if ($module === 'MeetingsAndEvents') $singular = 'MeetingsAndEvent';
    if ($module === 'CmsPages') $singular = 'CmsPage'; 
    if ($module === 'Gallery') $singular = 'Gallery'; 

    $expectedCreate = 'Create' . $singular;
    $expectedEdit = 'Edit' . $singular;
    $expectedView = 'View' . $singular;
    
    // Find folder
    $dirs = glob('app/Filament/Pages/*', GLOB_ONLYDIR);
    $folder = '';
    foreach ($dirs as $dir) {
        if (stripos(basename($dir), $module) !== false) {
            $folder = basename($dir);
            break;
        }
    }
    if (!$folder) $folder = $module;

    $changed = false;

    // Replace Create
    $content = preg_replace_callback('/->url\(\\\\App\\\\Filament\\\\Pages\\\\([a-zA-Z]+)\\\\([a-zA-Z]+)::getUrl\(\)\)/', function($m) use ($folder, $expectedCreate, &$changed) {
        if (strpos($m[2], 'Create') !== false || strpos($m[2], 'Add') !== false) {
            if ($m[2] !== $expectedCreate || $m[1] !== $folder) {
                $changed = true;
                return "->url(\\App\\Filament\\Pages\\$folder\\$expectedCreate::getUrl())";
            }
        }
        return $m[0];
    }, $content);

    // Replace View
    $content = preg_replace_callback('/->url\(fn\s*\([^)]*\)\s*=>\s*\\\\App\\\\Filament\\\\Pages\\\\([a-zA-Z]+)\\\\([a-zA-Z]+)::getUrl\(\[\'record\'\s*=>\s*\$arguments\[\'id\'\]\s*\?\?\s*0\]\)\)/', function($m) use ($folder, $expectedView, &$changed) {
        if (strpos($m[2], 'View') !== false) {
            if ($m[2] !== $expectedView || $m[1] !== $folder) {
                $changed = true;
                return "->url(fn (array \$arguments) => \\App\\Filament\\Pages\\$folder\\$expectedView::getUrl(['record' => \$arguments['id'] ?? 0]))";
            }
        }
        return $m[0];
    }, $content);

    // Replace Edit
    $content = preg_replace_callback('/->url\(fn\s*\([^)]*\)\s*=>\s*\\\\App\\\\Filament\\\\Pages\\\\([a-zA-Z]+)\\\\([a-zA-Z]+)::getUrl\(\[\'record\'\s*=>\s*\$arguments\[\'id\'\]\s*\?\?\s*0\]\)\)/', function($m) use ($folder, $expectedEdit, &$changed) {
        if (strpos($m[2], 'Edit') !== false) {
            if ($m[2] !== $expectedEdit || $m[1] !== $folder) {
                $changed = true;
                return "->url(fn (array \$arguments) => \\App\\Filament\\Pages\\$folder\\$expectedEdit::getUrl(['record' => \$arguments['id'] ?? 0]))";
            }
        }
        return $m[0];
    }, $content);

    if ($changed) {
        file_put_contents($file, $content);
        echo "Fixed classes in $file\n";
        $count++;
    }
}

echo "Fixed $count files.";
