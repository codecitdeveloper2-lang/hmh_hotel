<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$brand = ['id' => 13];
$html = \Illuminate\Support\Facades\Blade::render(<<<'BLADE'
    <x-filament::dropdown.list>
        <x-filament::dropdown.list.item
            icon="heroicon-m-eye"
            tag="a" href="{{ \App\Filament\Pages\Brands\ViewBrand::getUrl(['record' => $brand['id']]) }}"
        >
            View
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item
            icon="heroicon-m-pencil-square"
            tag="a" href="{{ \App\Filament\Pages\Brands\EditBrand::getUrl(['record' => $brand['id']]) }}"
        >
            Edit
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
BLADE, ['brand' => $brand]);

echo "---RENDERED HTML---\n";
echo $html;
