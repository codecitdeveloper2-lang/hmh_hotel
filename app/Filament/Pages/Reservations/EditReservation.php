<?php
namespace App\Filament\Pages\Reservations;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditReservation extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-reservations/{record}/edit';
    

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
        return $form->schema(\App\Filament\Pages\ManageReservations::getViewReservationFormSchema())->statePath('data');
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageReservations::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageReservations::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
