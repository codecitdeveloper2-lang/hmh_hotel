<?php
namespace App\Filament\Pages\Destinations;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateDestination extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-destinations/create';
    

    public ?array $data = [];

    public function mount(): void
    {
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageDestinations::getDestinationFormSchema())->statePath('data');
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageDestinations::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageDestinations::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
