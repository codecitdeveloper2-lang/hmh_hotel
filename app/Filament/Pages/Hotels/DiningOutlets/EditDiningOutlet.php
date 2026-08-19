<?php
namespace App\Filament\Pages\Hotels\DiningOutlets;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditDiningOutlet extends Page implements HasForms
{
    use HasHotelTabs;
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/dining-outlets/{outlet_id}/edit';
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
            ->statePath('data')
            ->model(\App\Models\DiningOutlet::find($this->outlet_id));
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $d = \App\Models\DiningOutlet::find($this->outlet_id);
        if ($d) {
            $name = is_array($d->name) ? $d->name : ['en' => $d->name];
            $name['en'] = $data['name'] ?? '';
            $cuisine = is_array($d->cuisine_type) ? $d->cuisine_type : ['en' => $d->cuisine_type];
            $cuisine['en'] = $data['cuisine_type'] ?? '';
            $opening = is_array($d->opening_hours) ? $d->opening_hours : ['en' => $d->opening_hours];
            $opening['en'] = $data['opening_hours'] ?? '';
            $d->update([
                'name' => $name,
                'slug' => $data['slug'] ?? $d->slug,
                'cuisine_type' => $cuisine,
                'opening_hours' => $opening,
                'has_table_booking' => $data['has_table_booking'] ?? false,
                'is_active' => ($data['status'] ?? 'active') === 'active',
                'read_more_label' => $data['read_more_label'] ?? null,
                'read_more_link' => $data['read_more_link'] ?? null,
            ]);

            $d->seoMetadata()->updateOrCreate(
                [],
                [
                    'meta_title' => $data['meta_title'] ?? '',
                    'meta_description' => $data['meta_description'] ?? '',
                ]
            );
            
            $this->form->model($d)->saveRelationships();
        }
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets::getUrl(['record' => $this->record]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets::getUrl(['record' => $this->record]); }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('manage_dining_details')
                ->label('Manage Dining Details')
                ->url(fn () => \App\Filament\Pages\Hotels\DiningOutlets\ManageDiningDetails::getUrl(['record' => $this->record, 'outlet_id' => $this->outlet_id]))
                ->color('primary')
                ->icon('heroicon-m-document-text'),
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
