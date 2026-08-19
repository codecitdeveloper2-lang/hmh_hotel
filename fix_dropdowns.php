<?php
$dir = __DIR__ . '/resources/views/filament/pages';
foreach (glob($dir . '/*.blade.php') as $file) {
    $content = file_get_contents($file);
    if (strpos($content, '<x-filament::dropdown placement="bottom-end">') !== false) {
        $content = str_replace('<x-filament::dropdown placement="bottom-end">', '<x-filament::dropdown placement="bottom-end" teleport>', $content);
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
