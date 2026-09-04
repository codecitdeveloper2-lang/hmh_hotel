<?php
namespace App\Filament\Pages\Hotels\RoomTypes;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditRoomType extends Page implements HasForms
{
    use HasHotelTabs;
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
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $this->room_id = $room_id;
        $room = \App\Models\RoomType::find($room_id);
        if ($room) {
            $this->form->fill([
                'hotel' => (int) $this->record,
                'name' => is_array($room->name) ? ($room->name['en'] ?? '') : $room->name,
                'slug' => $room->slug,
                'short_description' => is_array($room->short_description) ? ($room->short_description['en'] ?? '') : $room->short_description,
                'description' => is_array($room->description) ? ($room->description['en'] ?? '') : $room->description,
                'status' => $room->is_active ? 'active' : 'inactive',
                'read_more_label' => $room->read_more_label,
                'read_more_link' => $room->read_more_link,
                'book_now_label' => $room->book_now_label,
                'book_now_link' => $room->book_now_link,
                'starting_price' => $room->starting_price ?? '',
                'room_area' => $room->size_sqm ? $room->size_sqm . ' m²' : '',
                'bed_type' => $room->bed_type ?? '',
                'special_features' => $room->special_features ?? [['icon' => '', 'feature_name' => '']],
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
            ->statePath('data')
            ->model(\App\Models\RoomType::find($roomId));
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $room = \App\Models\RoomType::find($this->room_id);
        if ($room) {
            $existing = $room->name ?? [];
            $name = is_array($existing) ? $existing : ['en' => $existing];
            $name['en'] = $data['name'] ?? ($name['en'] ?? '');

            $existingShortDesc = $room->short_description ?? [];
            $shortDesc = is_array($existingShortDesc) ? $existingShortDesc : ['en' => $existingShortDesc];
            $shortDesc['en'] = $data['short_description'] ?? ($shortDesc['en'] ?? '');

            $existingDesc = $room->description ?? [];
            $desc = is_array($existingDesc) ? $existingDesc : ['en' => $existingDesc];
            $desc['en'] = $data['description'] ?? ($desc['en'] ?? '');

            // extract just the numbers from room_area string
            $sizeSqm = null;
            if (isset($data['room_area'])) {
                preg_match('/(\d+)/', $data['room_area'], $matches);
                if (isset($matches[1])) {
                    $sizeSqm = (int) $matches[1];
                }
            }

            $room->update([
                'name' => $name,
                'slug' => $data['slug'] ?? $room->slug,
                'short_description' => $shortDesc,
                'description' => $desc,
                'is_active' => ($data['status'] ?? 'active') === 'active',
                'read_more_label' => $data['read_more_label'] ?? null,
                'read_more_link' => $data['read_more_link'] ?? null,
                'book_now_label' => $data['book_now_label'] ?? null,
                'book_now_link' => $data['book_now_link'] ?? null,
                'starting_price' => $data['starting_price'] ?? null,
                'size_sqm' => $sizeSqm,
                'bed_type' => $data['bed_type'] ?? null,
                'special_features' => $data['special_features'] ?? null,
            ]);
            
            $room->seoMetadata()->updateOrCreate(
                [],
                [
                    'meta_title' => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                ]
            );
            
            $this->form->model($room)->saveRelationships();
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
