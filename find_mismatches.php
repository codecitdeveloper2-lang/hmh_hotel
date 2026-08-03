<?php
$files = glob('app/Filament/Pages/Manage*.php');
$badRefs = [];

foreach ($files as $file) {
    $content = file_get_contents($file);
    preg_match_all('/\\\\App\\\\Filament\\\\Pages\\\\[a-zA-Z]+\\\\([a-zA-Z]+)::getUrl/', $content, $matches);
    foreach ($matches[1] as $class) {
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
        if ($module === 'CmsPages') $singular = 'CmsPage'; // <-- Here is where the mismatch is!
        
        $expectedCreate = 'Create' . $singular;
        $expectedEdit = 'Edit' . $singular;
        $expectedView = 'View' . $singular;
        
        if (!in_array($class, [$expectedCreate, $expectedEdit, $expectedView])) {
            $badRefs[] = "$file has $class (Expected: $expectedCreate / $expectedEdit / $expectedView)";
        }
    }
}
print_r($badRefs);
