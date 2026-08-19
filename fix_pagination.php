<?php
$dir = __DIR__ . '/resources/views/filament/pages';
foreach (glob($dir . '/*.blade.php') as $file) {
    $content = file_get_contents($file);
    
    // Check if it has the x-filament::section for pagination
    if (strpos($content, '<x-filament::section>') !== false && strpos($content, '{{-- Pagination --}}') !== false) {
        $content = str_replace(
            "{{-- Pagination --}}\n        <x-filament::section>",
            "{{-- Pagination --}}\n        <div class=\"px-4 py-3 border-t border-gray-200 dark:border-white/10\">",
            $content
        );
        $content = str_replace(
            "{{-- Pagination --}}\r\n        <x-filament::section>",
            "{{-- Pagination --}}\r\n        <div class=\"px-4 py-3 border-t border-gray-200 dark:border-white/10\">",
            $content
        );
        
        // Only replace the last occurrence of </x-filament::section> which is the pagination one
        $pos = strrpos($content, '</x-filament::section>');
        if ($pos !== false) {
            $content = substr_replace($content, '</div>', $pos, strlen('</x-filament::section>'));
        }
        
        file_put_contents($file, $content);
        echo "Updated pagination container in $file\n";
    }
}
