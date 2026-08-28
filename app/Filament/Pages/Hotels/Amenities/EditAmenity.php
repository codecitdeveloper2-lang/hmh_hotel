<?php
namespace App\Filament\Pages\Hotels\Amenities;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditAmenity extends Page implements HasForms
{
    use HasHotelTabs;
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
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $this->amenity_id = $amenity_id;
        
        $a = \App\Models\Amenity::find($amenity_id);
        if ($a) {
            $this->form->fill([
                'title' => is_array($a->title) ? ($a->title['en'] ?? '') : $a->title,
                'description' => $a->description,
                'read_more_label' => $a->read_more_label,
                'read_more_link' => $a->read_more_link,
                'call_us_no' => $a->call_us_no,
                'amenities_list' => is_string($a->amenities_list) ? json_decode($a->amenities_list, true) : ($a->amenities_list ?? []),
            ]);
        }
    }



    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\Hotels\Amenities\ListAmenities::getAmenityFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $a = \App\Models\Amenity::find($this->amenity_id);
        if ($a) {
            $a->update([
                'title' => ['en' => $data['title']],
                'description' => $data['description'] ?? null,
                'read_more_label' => $data['read_more_label'] ?? null,
                'read_more_link' => $data['read_more_link'] ?? null,
                'call_us_no' => $data['call_us_no'] ?? null,
                'amenities_list' => $data['amenities_list'] ?? [],
            ]);
            \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        }
        $this->redirect(\App\Filament\Pages\Hotels\Amenities\ListAmenities::getUrl(['record' => $this->record]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\Amenities\ListAmenities::getUrl(['record' => $this->record]); }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('details_page')
                ->label('Manage Amenity Details')
                ->url(fn () => \App\Filament\Pages\Hotels\Amenities\AmenityDetailsContent::getUrl(['record' => $this->record, 'amenity_id' => $this->amenity_id]))
                ->color('primary')
                ->icon('heroicon-m-document-text'),
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
