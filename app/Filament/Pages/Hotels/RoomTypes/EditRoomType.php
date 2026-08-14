<?php
namespace App\Filament\Pages\Hotels\RoomTypes;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditRoomType extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/room-types/{room_id}/edit';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public $record;
    public $room_id;
    public ?array $data = [];

    public function mount($record, $room_id = null): void
    {
        $this->record = $record;
        $this->room_id = $room_id;
        $room = \App\Models\RoomType::find($room_id);
        if ($room) {
            $this->form->fill([
                'hotel' => (int) $this->record,
                'name' => is_array($room->name) ? ($room->name['en'] ?? '') : $room->name,
                'slug' => $room->slug,
                'description' => is_array($room->description) ? ($room->description['en'] ?? '') : $room->description,
                'status' => $room->is_active ? 'active' : 'inactive',
                'meta_title' => is_array($room->meta_title) ? ($room->meta_title['en'] ?? '') : $room->meta_title,
                'meta_description' => is_array($room->meta_description) ? ($room->meta_description['en'] ?? '') : $room->meta_description,
            ]);
        }
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes::getRoomTypeFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $room = \App\Models\RoomType::find($this->room_id);
        if ($room) {
            $existing = $room->name ?? [];
            $name = is_array($existing) ? $existing : ['en' => $existing];
            $name['en'] = $data['name'] ?? ($name['en'] ?? '');

            $existingDesc = $room->description ?? [];
            $desc = is_array($existingDesc) ? $existingDesc : ['en' => $existingDesc];
            $desc['en'] = $data['description'] ?? ($desc['en'] ?? '');

            $room->update([
                'name' => $name,
                'slug' => $data['slug'] ?? $room->slug,
                'description' => $desc,
                'is_active' => ($data['status'] ?? 'active') === 'active',
            ]);
        }
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes::getUrl(['record' => $this->record]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('manage_room_details')
                ->label('Manage Room Details')
                ->url(fn () => url('/hotel-management/manage-hotels/' . $this->record . '/room-types/' . $this->room_id . '/details'))
                ->color('primary')
                ->icon('heroicon-m-document-text'),
        ];
    }
}
