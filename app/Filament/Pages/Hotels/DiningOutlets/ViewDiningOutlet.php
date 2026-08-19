<?php
namespace App\Filament\Pages\Hotels\DiningOutlets;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewDiningOutlet extends Page implements HasForms
{
    use HasHotelTabs;
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
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $this->outlet_id = $outlet_id;
        $d = \App\Models\DiningOutlet::find($outlet_id);
        if ($d) {
            $this->form->fill([
                'hotel' => (int) $this->record,
                'name' => is_array($d->name) ? ($d->name['en'] ?? '') : $d->name,
                'slug' => $d->slug,
                'cuisine_type' => is_array($d->cuisine_type) ? ($d->cuisine_type['en'] ?? '') : $d->cuisine_type,
                'opening_hours' => is_array($d->opening_hours) ? ($d->opening_hours['en'] ?? '') : $d->opening_hours,
                'has_table_booking' => $d->has_table_booking,
                'status' => $d->is_active ? 'active' : 'inactive',
                'read_more_label' => $d->read_more_label,
                'read_more_link' => $d->read_more_link,
                'meta_title' => $d->seoMetadata->meta_title ?? '',
                'meta_description' => $d->seoMetadata->meta_description ?? '',
            ]);
        }
    }



    public function form($form)
    {
        return $form
            ->schema(\App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets::getDiningOutletFormSchema())
            ->disabled()
            ->statePath('data')
            ->model(\App\Models\DiningOutlet::find($this->outlet_id));
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
