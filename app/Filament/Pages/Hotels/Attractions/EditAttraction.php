<?php
namespace App\Filament\Pages\Hotels\Attractions;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditAttraction extends Page implements HasForms
{
    use HasHotelTabs;
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/attractions/{attraction_id}/edit';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public $record;
    public $attraction_id;
    public ?array $data = [];

    public function mount($record, $attraction_id = null): void
    {
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $this->attraction_id = $attraction_id;
        $a = \App\Models\Attraction::find($attraction_id);
        if ($a) {
            $this->form->fill([
                'hotel' => (int) $this->record,
                'name' => is_array($a->name) ? ($a->name['en'] ?? '') : $a->name,
                'slug' => $a->slug,
                'category' => $a->category,
                'description' => is_array($a->description) ? ($a->description['en'] ?? '') : $a->description,
                'address' => $a->address,
                'latitude' => $a->latitude,
                'longitude' => $a->longitude,
                'status' => $a->is_active ? 'active' : 'inactive',
                'read_more_label' => $a->read_more_label,
                'read_more_link' => $a->read_more_link,
                'meta_title' => $a->seoMetadata->meta_title ?? '',
                'meta_description' => $a->seoMetadata->meta_description ?? '',
            ]);
        }
    }



    public function form($form)
    {
        return $form
            ->schema(\App\Filament\Pages\Hotels\Attractions\ListAttractions::getAttractionFormSchema())
            ->statePath('data')
            ->model(\App\Models\Attraction::find($this->attraction_id));
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $a = \App\Models\Attraction::find($this->attraction_id);
        if ($a) {
            $name = is_array($a->name) ? $a->name : ['en' => $a->name];
            $name['en'] = $data['name'] ?? '';
            $desc = is_array($a->description) ? $a->description : ['en' => $a->description];
            $desc['en'] = $data['description'] ?? '';
            $a->update([
                'name' => $name,
                'slug' => $data['slug'] ?? $a->slug,
                'category' => $data['category'] ?? null,
                'description' => $desc,
                'address' => $data['address'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'read_more_label' => $data['read_more_label'] ?? null,
                'read_more_link' => $data['read_more_link'] ?? null,
                'is_active' => ($data['status'] ?? 'active') === 'active',
            ]);
            
            $a->seoMetadata()->updateOrCreate(
                [],
                [
                    'meta_title' => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                ]
            );
            
            $this->form->model($a)->saveRelationships();
        }
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\Attractions\ListAttractions::getUrl(['record' => $this->record]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\Attractions\ListAttractions::getUrl(['record' => $this->record]); }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
