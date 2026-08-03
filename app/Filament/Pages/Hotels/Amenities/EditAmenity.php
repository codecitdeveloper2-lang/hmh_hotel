<?php
namespace App\Filament\Pages\Hotels\Amenities;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditAmenity extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/amenities/{amenity_id}/edit';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public $record;
    public $amenity_id;
    public ?array $data = [];

    public function mount($record, $amenity_id): void
    {
        $this->record = $record;
        $this->amenity_id = $amenity_id;
        
        $mockData = \App\Filament\Pages\Hotels\Amenities\ListAmenities::getMockAmenities();
        $this->data = $mockData[$this->amenity_id] ?? [];
        $this->form->fill($this->data);
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    public function form(Form $form): Form
    {
        return $form->schema(\App\Filament\Pages\Hotels\Amenities\ListAmenities::getAmenityFormSchema())->statePath('data');
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\Amenities\ListAmenities::getUrl(['record' => $this->record]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\Amenities\ListAmenities::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}