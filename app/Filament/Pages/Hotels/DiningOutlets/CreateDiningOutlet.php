<?php
namespace App\Filament\Pages\Hotels\DiningOutlets;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateDiningOutlet extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/dining-outlets/create';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public ?array $data = [];

    public $record;

    public function mount($record): void
    {
        $this->record = $record;
        $this->form->fill([
            'hotel' => (int) $this->record,
        ]);
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
        \App\Models\DiningOutlet::create([
            'property_id' => $this->record,
            'name' => ['en' => $data['name'] ?? ''],
            'slug' => $data['slug'] ?? \Illuminate\Support\Str::slug($data['name'] ?? ''),
            'cuisine_type' => ['en' => $data['cuisine_type'] ?? ''],
            'is_active' => ($data['status'] ?? 'active') === 'active',
            'sort_order' => 0,
        ]);
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
