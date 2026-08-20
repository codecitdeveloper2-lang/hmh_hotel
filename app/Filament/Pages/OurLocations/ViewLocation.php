<?php
namespace App\Filament\Pages\OurLocations;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use App\Models\OurLocation;

class ViewLocation extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-our-locations/{record}/view';

    public ?array $data = [];

    public function mount($record): void
    {
        $location = OurLocation::find((int)$record);
        if ($location) {
            $this->form->fill($location->toArray());
        }
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageOurLocations::getLocationFormSchema())->disabled()->statePath('data');
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageOurLocations::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
