<?php
namespace App\Filament\Pages\Hotels\DiningOutlets;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditDiningOutlet extends Page implements HasForms
{
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
        return $form->schema(\App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets::getDiningOutletFormSchema())->statePath('data');
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
            $d->update([
                'name' => $name,
                'slug' => $data['slug'] ?? $d->slug,
                'cuisine_type' => $cuisine,
                'is_active' => ($data['status'] ?? 'active') === 'active',
            ]);
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
