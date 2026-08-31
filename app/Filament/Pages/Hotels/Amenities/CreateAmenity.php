<?php
namespace App\Filament\Pages\Hotels\Amenities;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateAmenity extends Page implements HasForms
{
    use HasHotelTabs;
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/amenities/create';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public ?array $data = [];

    public $record;

    public function mount($record): void
    {
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $this->form->fill();
    }



    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\Hotels\Amenities\ListAmenities::getAmenityFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $a = \App\Models\Amenity::create([
            'property_id' => $this->record,
            'title' => ['en' => $data['title']],
            'description' => $data['description'] ?? null,
            'read_more_label' => $data['read_more_label'] ?? null,
            'read_more_link' => $data['read_more_link'] ?? null,
            'call_us_no' => $data['call_us_no'] ?? null,
            'amenities_list' => $data['amenities_list'] ?? [],
            'is_active' => 1,
            'display_order' => 0,
        ]);

        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\Amenities\ListAmenities::getUrl(['record' => $this->record]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\Amenities\ListAmenities::getUrl(['record' => $this->record]); }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('details_page')
                ->label('Details Page')
                ->disabled()
                ->tooltip('Save the amenity first to access its details content page.')
                ->color('gray')
                ->icon('heroicon-m-document-text'),
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
