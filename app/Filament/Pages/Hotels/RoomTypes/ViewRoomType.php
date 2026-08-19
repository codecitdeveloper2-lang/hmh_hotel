<?php
namespace App\Filament\Pages\Hotels\RoomTypes;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewRoomType extends Page implements HasForms
{
    use HasHotelTabs;
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
        $this->mountHasHotelTabs($record);
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
                'read_more_label' => $room->read_more_label,
                'read_more_link' => $room->read_more_link,
                'book_now_label' => $room->book_now_label,
                'book_now_link' => $room->book_now_link,
                'meta_title' => $room->seoMetadata->meta_title ?? '',
                'meta_description' => $room->seoMetadata->meta_description ?? '',
            ]);
        }
    }



    public function form($form)
    {
        $roomId = $this->room_id ?? request()->route('room_id');
        return $form
            ->schema(\App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes::getRoomTypeFormSchema())
            ->disabled()
            ->statePath('data')
            ->model(\App\Models\RoomType::find($roomId));
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
