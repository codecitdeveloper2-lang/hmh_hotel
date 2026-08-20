<?php
namespace App\Filament\Pages\OurLocations;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use App\Models\OurLocation;
use Filament\Notifications\Notification;

class EditLocation extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-our-locations/{record}/edit';

    public ?array $data = [];
    public ?int $recordId = null;

    public function mount($record): void
    {
        $this->recordId = (int)$record;
        $location = OurLocation::find($this->recordId);
        if ($location) {
            $this->form->fill($location->toArray());
        }
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageOurLocations::getLocationFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $location = OurLocation::find($this->recordId);
        if ($location) {
            $location->update($data);
        }

        Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageOurLocations::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageOurLocations::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
