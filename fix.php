<?php
// Fix data binding in ALL forms (Create/Edit/View) by adding ->statePath('data')

$files = glob('app/Filament/Pages/*/*.php');
$count = 0;

foreach ($files as $file) {
    $c = file_get_contents($file);
    
    // Only apply if not already there
    if (strpos($c, "->statePath('data')") === false) {
        $c = preg_replace(
            '/return\s+\$form->schema\((.*?)\)(->disabled\(\))?;/s',
            "return \$form->schema($1)$2->statePath('data');",
            $c
        );
        file_put_contents($file, $c);
        $count++;
        echo "Fixed $file\n";
    }
}

echo "\nDone fixing $count files.";
