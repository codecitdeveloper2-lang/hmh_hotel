<?php
namespace App\Filament\Pages\Hotels\RoomTypes;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateRoomType extends Page implements HasForms
{
    use HasHotelTabs;
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/room-types/create';
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
            ->schema(\App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes::getRoomTypeFormSchema())
            ->statePath('data')
            ->model(\App\Models\RoomType::class);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $room = \App\Models\RoomType::create([
            'property_id' => $this->record,
            'name' => ['en' => $data['name'] ?? ''],
            'slug' => $data['slug'] ?? \Illuminate\Support\Str::slug($data['name'] ?? ''),
            'description' => ['en' => $data['description'] ?? ''],
            'is_active' => ($data['status'] ?? 'active') === 'active',
            'read_more_label' => $data['read_more_label'] ?? null,
            'read_more_link' => $data['read_more_link'] ?? null,
            'book_now_label' => $data['book_now_label'] ?? null,
            'book_now_link' => $data['book_now_link'] ?? null,
            'sort_order' => 0,
        ]);
        
        $room->seoMetadata()->create([
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ]);
        
        $this->form->model($room)->saveRelationships();

        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes::getUrl(['record' => $this->record]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes::getUrl(['record' => $this->record]); }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('manage_room_details')
                ->label('Manage Room Details')
                ->disabled()
                ->tooltip('Please save the room type before managing its details.')
                ->color('primary')
                ->icon('heroicon-m-document-text'),
        ];
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
