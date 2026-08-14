<?php
namespace App\Filament\Pages\Brands;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewBrand extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-brands/{record}/view';
    

    public $record;
    public string $activeLocale = 'en';
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $property = \App\Models\Property::findOrFail($record);
        $data = $property->toArray();

        // Keep nested arrays so locale-aware fields (name.en / name.ar) are populated
        $this->form->fill($data);
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        $property = \App\Models\Property::find($this->record);
        return $property?->display_name ?? 'View Brand';
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageBrands::getBrandFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageBrands::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
