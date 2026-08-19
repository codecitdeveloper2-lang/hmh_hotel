<?php
namespace App\Filament\Pages\Hotels\DiningOutlets;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateDiningOutlet extends Page implements HasForms
{
    use HasHotelTabs;
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/dining-outlets/create';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public ?array $data = [];

    public $record;

    public function mount($record): void
    {
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $this->form->fill([
            'hotel' => (int) $this->record,
        ]);
    }



    public function form($form)
    {
        return $form
            ->schema(\App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets::getDiningOutletFormSchema())
            ->statePath('data')
            ->model(\App\Models\DiningOutlet::class);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $d = \App\Models\DiningOutlet::create([
            'property_id' => $this->record,
            'name' => ['en' => $data['name'] ?? ''],
            'slug' => $data['slug'] ?? \Illuminate\Support\Str::slug($data['name'] ?? ''),
            'cuisine_type' => ['en' => $data['cuisine_type'] ?? ''],
            'opening_hours' => ['en' => $data['opening_hours'] ?? ''],
            'has_table_booking' => $data['has_table_booking'] ?? false,
            'is_active' => ($data['status'] ?? 'active') === 'active',
            'read_more_label' => $data['read_more_label'] ?? null,
            'read_more_link' => $data['read_more_link'] ?? null,
            'sort_order' => 0,
        ]);

        $d->seoMetadata()->create([
            'meta_title' => $data['meta_title'] ?? '',
            'meta_description' => $data['meta_description'] ?? '',
        ]);
        
        $this->form->model($d)->saveRelationships();
        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets::getUrl(['record' => $this->record]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets::getUrl(['record' => $this->record]); }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('manage_dining_details')
                ->label('Manage Dining Details')
                ->disabled()
                ->tooltip('Please save the dining outlet before managing its details content.')
                ->color('primary')
                ->icon('heroicon-m-document-text'),
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
