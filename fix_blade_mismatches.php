<?php
$bladeFiles = glob('resources/views/filament/pages/manage-*.blade.php');

$classMap = [];
$phpFiles = glob('app/Filament/Pages/*/*.php');
foreach ($phpFiles as $file) {
    $basename = basename($file, '.php');
    if (strpos($basename, 'Create') === 0 || strpos($basename, 'Edit') === 0 || strpos($basename, 'View') === 0) {
        $folder = basename(dirname($file));
        $actionType = '';
        if (strpos($basename, 'Create') === 0) $actionType = 'Create';
        if (strpos($basename, 'Edit') === 0) $actionType = 'Edit';
        if (strpos($basename, 'View') === 0) $actionType = 'View';
        
        $classMap[$folder][$actionType] = $basename;
    }
}

$count = 0;
foreach ($bladeFiles as $file) {
    $c = file_get_contents($file);
    $changed = false;

    // We look for href="{{ \App\Filament\Pages\Folder\WrongClass::getUrl(...) }}"
    // and replace WrongClass with the correct one from $classMap.
    
    $c = preg_replace_callback('/\\\\App\\\\Filament\\\\Pages\\\\([a-zA-Z0-9_]+)\\\\([a-zA-Z0-9_]+)::getUrl/', function($m) use ($classMap, &$changed) {
        $folder = $m[1];
        $className = $m[2];
        
        $actionType = '';
        if (strpos($className, 'Create') === 0) $actionType = 'Create';
        if (strpos($className, 'Edit') === 0) $actionType = 'Edit';
        if (strpos($className, 'View') === 0) $actionType = 'View';
        
        if ($actionType && isset($classMap[$folder][$actionType])) {
            $correctClass = $classMap[$folder][$actionType];
            if ($className !== $correctClass) {
                $changed = true;
                return "\\App\\Filament\\Pages\\$folder\\$correctClass::getUrl";
            }
        }
        
        return $m[0];
    }, $c);

    if ($changed) {
        file_put_contents($file, $c);
        $count++;
        echo "Fixed blade: $file\n";
    }
}
echo "Fixed $count blade files.";
