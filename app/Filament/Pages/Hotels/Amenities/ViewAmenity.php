<?php
namespace App\Filament\Pages\Hotels\Amenities;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewAmenity extends Page implements HasForms
{
    use HasHotelTabs;
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/amenities/{amenity_id}/view';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public $record;
    public $amenity_id;
    public ?array $data = [];

    public function mount($record, $amenity_id): void
    {
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $this->amenity_id = $amenity_id;
        
        $mockData = \App\Filament\Pages\Hotels\Amenities\ListAmenities::getMockAmenities();
        $this->form->fill($mockData[$this->amenity_id] ?? []);
        $this->form->fill($this->data);
    }



    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\Hotels\Amenities\ListAmenities::getAmenityFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\Amenities\ListAmenities::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
