<?php
$files = glob('app/Filament/Pages/*/*.php');
foreach ($files as $file) {
    if (strpos($file, 'Create') !== false || strpos($file, 'Edit') !== false || strpos($file, 'View') !== false) {
        $c = file_get_contents($file);
        $c = str_replace('public function form(Form $form): Form', 'public function form($form)', $c);
        file_put_contents($file, $c);
        echo "Fixed $file\n";
    }
}
echo "Done fixing forms.";
