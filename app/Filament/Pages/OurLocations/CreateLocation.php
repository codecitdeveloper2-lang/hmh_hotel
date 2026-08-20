<?php
namespace App\Filament\Pages\OurLocations;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use App\Models\OurLocation;
use Filament\Notifications\Notification;

class CreateLocation extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-our-locations/create';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageOurLocations::getLocationFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        OurLocation::create($data);

        Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageOurLocations::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageOurLocations::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
