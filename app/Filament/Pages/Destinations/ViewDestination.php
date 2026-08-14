<?php
namespace App\Filament\Pages\Destinations;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewDestination extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-destinations/{record}/view';
    

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $mockData = \App\Filament\Pages\ManageDestinations::getMockDestinations();
        $this->form->fill($mockData[$this->record] ?? []);
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageDestinations::getDestinationFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageDestinations::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
