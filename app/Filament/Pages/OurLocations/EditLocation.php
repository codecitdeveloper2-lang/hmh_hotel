<?php
namespace App\Filament\Pages\OurLocations;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditLocation extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-our-locations/{record}/edit';

    public ?array $data = [];

    public function mount($record): void
    {
        $mockData = \App\Filament\Pages\ManageOurLocations::getMockOurLocations();
        $this->form->fill($mockData[$record] ?? []);
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageOurLocations::getLocationFormSchema())->statePath('data');
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Updated successfully (Mock)')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageOurLocations::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageOurLocations::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
