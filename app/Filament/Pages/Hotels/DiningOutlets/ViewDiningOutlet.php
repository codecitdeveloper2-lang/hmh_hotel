<?php
namespace App\Filament\Pages\Hotels\DiningOutlets;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewDiningOutlet extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/dining-outlets/{outlet_id}/view';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public $record;
    public $outlet_id;
    public ?array $data = [];

    public function mount($record, $outlet_id = null): void
    {
        $this->record = $record;
        $this->outlet_id = $outlet_id;
        $d = \App\Models\DiningOutlet::find($outlet_id);
        if ($d) {
            $this->form->fill([
                'hotel' => (int) $this->record,
                'name' => is_array($d->name) ? ($d->name['en'] ?? '') : $d->name,
                'slug' => $d->slug,
                'cuisine_type' => is_array($d->cuisine_type) ? ($d->cuisine_type['en'] ?? '') : $d->cuisine_type,
                'status' => $d->is_active ? 'active' : 'inactive',
            ]);
        }
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets::getDiningOutletFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
