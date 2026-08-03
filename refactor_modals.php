<?php

$dir = __DIR__ . '/app/Filament/Pages';
$files = glob($dir . '/Manage*.php');

$genericCreateView = <<<'HTML'
<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
        
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <x-filament::button type="submit">
                Save Changes
            </x-filament::button>
            <x-filament::button color="gray" tag="a" href="{{ $this->getBackUrl() }}">
                Cancel
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
HTML;

$genericViewView = <<<'HTML'
<x-filament-panels::page>
    {{ $this->form }}
    
    <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
        <x-filament::button color="gray" tag="a" href="{{ $this->getBackUrl() }}">
            Back
        </x-filament::button>
    </div>
</x-filament-panels::page>
HTML;

file_put_contents(__DIR__ . '/resources/views/filament/pages/generic-create-edit.blade.php', $genericCreateView);
file_put_contents(__DIR__ . '/resources/views/filament/pages/generic-view.blade.php', $genericViewView);

foreach ($files as $file) {
    $basename = basename($file, '.php');
    if (in_array($basename, ['ManageSettings', 'Dashboard'])) continue;

    $moduleName = str_replace('Manage', '', $basename);
    $singularName = rtrim($moduleName, 's');
    if ($moduleName === 'NewsAndPress') $singularName = 'NewsAndPress';
    if ($moduleName === 'GroupGallery') $singularName = 'GroupGallery';
    if ($moduleName === 'DiningOutlets') $singularName = 'DiningOutlet';
    if ($moduleName === 'RoomTypes') $singularName = 'RoomType';

    $content = file_get_contents($file);

    // Make methods static
    $content = preg_replace('/protected\s+function\s+get(\w+)FormSchema/', 'public static function get$1FormSchema', $content);
    $content = preg_replace('/public\s+function\s+get(\w+)FormSchema/', 'public static function get$1FormSchema', $content);
    $content = preg_replace('/public\s+function\s+getMock/', 'public static function getMock', $content);
    $content = preg_replace('/protected\s+function\s+getMock/', 'public static function getMock', $content);

    // Identify schemas and mock data methods available
    preg_match('/public static function get(\w+)FormSchema/', $content, $schemaMatch);
    preg_match('/public static function getMock(\w+)\(/', $content, $mockMatch);

    if (empty($schemaMatch)) continue;
    $schemaMethod = "get{$schemaMatch[1]}FormSchema";
    $mockMethod = !empty($mockMatch) ? "getMock{$mockMatch[1]}" : null;

    $kebabModule = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $moduleName));
    
    // Find cluster
    preg_match('/protected\s+static\s+\?string\s+\$cluster\s*=\s*(.*?);/', $content, $clusterMatch);
    $clusterAttr = $clusterMatch ? "protected static ?string \$cluster = {$clusterMatch[1]};" : "";

    $outDir = $dir . '/' . $moduleName;
    @mkdir($outDir, 0755, true);

    $ns = "App\\Filament\\Pages\\{$moduleName}";
    $managerClass = "\\App\\Filament\\Pages\\{$basename}";

    // CREATE PAGE
    $createCode = <<<PHP
<?php
namespace $ns;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class Create$singularName extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string \$view = 'filament.pages.generic-create-edit';
    protected static bool \$shouldRegisterNavigation = false;
    protected static ?string \$slug = 'manage-$kebabModule/create';
    $clusterAttr

    public ?array \$data = [];

    public function mount(): void
    {
        \$this->form->fill();
    }

    public function form(Form \$form): Form
    {
        return \$form->schema($managerClass::$schemaMethod());
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        \$this->redirect($managerClass::getUrl());
    }

    public function getBackUrl(): string { return $managerClass::getUrl(); }
}
PHP;

    file_put_contents("$outDir/Create{$singularName}.php", $createCode);

    if ($mockMethod) {
        // EDIT PAGE
        $editCode = <<<PHP
<?php
namespace $ns;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class Edit$singularName extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string \$view = 'filament.pages.generic-create-edit';
    protected static bool \$shouldRegisterNavigation = false;
    protected static ?string \$slug = 'manage-$kebabModule/{record}/edit';
    $clusterAttr

    public \$record;
    public ?array \$data = [];

    public function mount(\$record): void
    {
        \$this->record = \$record;
        \$mockData = $managerClass::$mockMethod();
        \$this->form->fill(\$mockData[\$record] ?? []);
    }

    public function form(Form \$form): Form
    {
        return \$form->schema($managerClass::$schemaMethod());
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        \$this->redirect($managerClass::getUrl());
    }

    public function getBackUrl(): string { return $managerClass::getUrl(); }
}
PHP;
        file_put_contents("$outDir/Edit{$singularName}.php", $editCode);

        // VIEW PAGE
        $viewCode = <<<PHP
<?php
namespace $ns;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class View$singularName extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string \$view = 'filament.pages.generic-view';
    protected static bool \$shouldRegisterNavigation = false;
    protected static ?string \$slug = 'manage-$kebabModule/{record}/view';
    $clusterAttr

    public \$record;
    public ?array \$data = [];

    public function mount(\$record): void
    {
        \$this->record = \$record;
        \$mockData = $managerClass::$mockMethod();
        \$this->form->fill(\$mockData[\$record] ?? []);
    }

    public function form(Form \$form): Form
    {
        return \$form->schema($managerClass::$schemaMethod())->disabled();
    }
    
    public function getBackUrl(): string { return $managerClass::getUrl(); }
}
PHP;
        file_put_contents("$outDir/View{$singularName}.php", $viewCode);
    }

    // Now, rewrite the Actions in $content to be redirects.
    // For Add
    $content = preg_replace_callback('/(Action::make\([\'"]add(\w+)[\'"]\).*?)->action\(/s', function($matches) use ($ns) {
        return $matches[1] . "\n            ->url(\\{$ns}\\Create{$matches[2]}::getUrl())\n            ->action("; 
    }, $content);

    // For Edit
    $content = preg_replace_callback('/(Action::make\([\'"]edit(\w+)[\'"]\).*?)->action\(/s', function($matches) use ($ns) {
        return $matches[1] . "\n            ->url(fn (array \$arguments) => \\{$ns}\\Edit{$matches[2]}::getUrl(['record' => \$arguments['id'] ?? 0]))\n            ->action("; 
    }, $content);

    // For View
    $content = preg_replace_callback('/(Action::make\([\'"]view(\w+)[\'"]\).*?)->action\(/s', function($matches) use ($ns) {
        return $matches[1] . "\n            ->url(fn (array \$arguments) => \\{$ns}\\View{$matches[2]}::getUrl(['record' => \$arguments['id'] ?? 0]))\n            ->action("; 
    }, $content);

    // Wait, the action(function(){...}) block is now dead code if it's a redirect, but we leave it. Or remove it?
    // URL action will override ->action() click. So it's fine!

    file_put_contents($file, $content);
    echo "Processed $moduleName\n";
}
