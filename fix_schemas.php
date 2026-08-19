<?php
$dir = new RecursiveDirectoryIterator('app/Filament');
$ite = new RecursiveIteratorIterator($dir);
foreach($ite as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'Filament\Schemas\Components') !== false) {
            $newContent = str_replace('Filament\Schemas\Components', 'Filament\Forms\Components', $content);
            file_put_contents($file->getPathname(), $newContent);
            echo "Replaced in " . $file->getPathname() . "\n";
        }
    }
}
