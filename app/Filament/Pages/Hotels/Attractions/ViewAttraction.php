<?php
namespace App\Filament\Pages\Hotels\Attractions;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewAttraction extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/attractions/{attraction_id}/view';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public $record;
    public $attraction_id;
    public ?array $data = [];

    public function mount($record, $attraction_id = null): void
    {
        $this->record = $record;
        $this->attraction_id = $attraction_id;
        $a = \App\Models\Attraction::find($attraction_id);
        if ($a) {
            $this->form->fill([
                'hotel' => (int) $this->record,
                'name' => is_array($a->name) ? ($a->name['en'] ?? '') : $a->name,
                'slug' => $a->slug,
                'description' => is_array($a->description) ? ($a->description['en'] ?? '') : $a->description,
                'status' => $a->is_active ? 'active' : 'inactive',
            ]);
        }
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\Hotels\Attractions\ListAttractions::getAttractionFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\Attractions\ListAttractions::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
