<?php
namespace App\Filament\Pages\Hotels;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;

class Overview extends Page implements HasForms
{
    use InteractsWithForms, HasHotelTabs;
    
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/overview';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public string $activeLocale = 'en';
    public ?array $data = [];

    public function mount($record): void
    {
        $this->mountHasHotelTabs($record);
        $property = \App\Models\Property::findOrFail($record);
        $data = $property->toArray();

        // No need to extract English values, we use data.name.en and data.name.ar now

        $this->form->fill($data);
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        $property = \App\Models\Property::find($this->record);
        return $property?->display_name ?? 'Overview';
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageHotels::getHotelFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageHotels::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
