<?php
namespace App\Filament\Pages\Reservations;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewReservation extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-reservations/{record}/view';
    

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $mockData = \App\Filament\Pages\ManageReservations::getMockReservations();
        $this->form->fill($mockData[$this->record] ?? []);
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageReservations::getViewReservationFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageReservations::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
