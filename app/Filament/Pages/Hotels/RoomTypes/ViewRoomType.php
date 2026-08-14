<?php
namespace App\Filament\Pages\Hotels\RoomTypes;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewRoomType extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/room-types/{room_id}/view';
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
        return $form->schema(\App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes::getRoomTypeFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
