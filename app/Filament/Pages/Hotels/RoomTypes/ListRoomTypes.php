<?php

namespace App\Filament\Pages\Hotels\RoomTypes;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;

class ListRoomTypes extends Page
{
    use HasHotelTabs;

    protected string $view = 'filament.pages.manage-room-types';

    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;
    protected static ?string $slug = 'manage-hotels/{record}/room-types';
    protected static bool $shouldRegisterNavigation = false;

    public function mount($record): void
    {
        $this->mountHasHotelTabs($record);
    }


    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedPerPage(): void { $this->currentPage = 1; }

    public function nextPage(int $lastPage): void
    {
        if ($this->currentPage < $lastPage) $this->currentPage++;
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) $this->currentPage--;
    }

    public function gotoPage(int $page): void
    {
        $this->currentPage = $page;
    }

    protected function getViewData(): array
    {
        $query = \App\Models\RoomType::where('property_id', $this->record)
            ->orderBy('sort_order')
            ->orderBy('id');

        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        $rooms       = $query->forPage($currentPage, $this->perPage)->get()->map(function ($r) {
            $propName = $r->property ? (is_array($r->property->name) ? ($r->property->name['en'] ?? '') : $r->property->name) : '';
            return [
                'id' => $r->id,
                'name' => is_array($r->name) ? ($r->name['en'] ?? '') : $r->name,
                'hotel' => $propName,
                'room_type_id' => 'RT-' . str_pad($r->id, 4, '0', STR_PAD_LEFT),
                'max_adults' => 2,
                'max_children' => 1,
                'room_size' => '30 sqm',
                'bed_type' => 'King',
                'status' => $r->is_active ? 'Active' : 'Inactive',
            ];
        });
        $from        = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to          = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'rooms', 'from', 'to');
    }



    public function getTitle(): string | Htmlable
    {
        return 'Room Types';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Room Types';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage room categories available for each hotel.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addRoomType')
                ->label('Add Room Type')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getRoomTypeFormSchema())
                
            ->url(fn () => \App\Filament\Pages\Hotels\RoomTypes\CreateRoomType::getUrl(['record' => $this->record]))
            ->action(function (array $data) {
                    Notification::make()
                        ->title('Room Type Created')
                        ->body('The room type has been created successfully (Mock).')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewRoomTypeAction(): Action
    {
        return Action::make('viewRoomType')
            ->modalHeading('View Room Type')
            ->modalWidth('7xl')
            ->form($this->getRoomTypeFormSchema())
            ->fillForm(function (array $arguments) {
                $room = \App\Models\RoomType::find($arguments['id']);
                if (!$room) return [];
                return [
                    'name' => is_array($room->name) ? ($room->name['en'] ?? '') : $room->name,
                    'slug' => $room->slug,
                    'description' => is_array($room->description) ? ($room->description['en'] ?? '') : $room->description,
                    'status' => $room->is_active ? 'active' : 'inactive',
                    'meta_title' => is_array($room->meta_title) ? ($room->meta_title['en'] ?? '') : $room->meta_title,
                    'meta_description' => is_array($room->meta_description) ? ($room->meta_description['en'] ?? '') : $room->meta_description,
                ];
            })
            ->disabledForm()
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\RoomTypes\ViewRoomType::getUrl(['record' => $this->record, 'room_id' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editRoomTypeAction(): Action
    {
        return Action::make('editRoomType')
            ->modalHeading('Edit Room Type')
            ->modalWidth('7xl')
            ->form($this->getRoomTypeFormSchema())
            ->fillForm(function (array $arguments) {
                $room = \App\Models\RoomType::find($arguments['id']);
                if (!$room) return [];
                return [
                    'name' => is_array($room->name) ? ($room->name['en'] ?? '') : $room->name,
                    'slug' => $room->slug,
                    'description' => is_array($room->description) ? ($room->description['en'] ?? '') : $room->description,
                    'status' => $room->is_active ? 'active' : 'inactive',
                    'meta_title' => is_array($room->meta_title) ? ($room->meta_title['en'] ?? '') : $room->meta_title,
                    'meta_description' => is_array($room->meta_description) ? ($room->meta_description['en'] ?? '') : $room->meta_description,
                ];
            })
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\RoomTypes\EditRoomType::getUrl(['record' => $this->record, 'room_id' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function deleteRoomTypeAction(): Action
    {
        return Action::make('deleteRoomType')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                \App\Models\RoomType::find($arguments['id'])?->delete();
                Notification::make()
                    ->title('Room Type Deleted')
                    ->success()
                    ->send();
            });
    }

    public static function getRoomTypeFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            Select::make('hotel')
                                ->label('Hotel')
                                ->options(fn () => \App\Models\Property::where('type', 'hotel')->get()->mapWithKeys(fn ($h) => [$h->id => is_array($h->name) ? ($h->name['en'] ?? '') : $h->name])->toArray())
                                ->default(fn () => request()->route('record') ?? null)
                                ->disabled()
                                ->dehydrated()
                                ->required(),
                            TextInput::make('name')
                                ->label('Room Name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required(),
                            \App\Filament\Forms\Components\JoditEditor::make('description')
                                ->label('Description'),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                ])
                                ->default('active')
                                ->required(),
                        ]),
                        
                    Section::make('Card / Listing Details')
                        ->schema([

                            Grid::make(2)->schema([
                                TextInput::make('read_more_label')
                                    ->label('Read More Label')
                                    ->default('READ MORE'),
                                TextInput::make('read_more_link')
                                    ->label('Read More Link (Optional)'),
                            ]),
                            Grid::make(2)->schema([
                                TextInput::make('book_now_label')
                                    ->label('Book Now Label')
                                    ->default('BOOK NOW'),
                                TextInput::make('book_now_link')
                                    ->label('Book Now Link (Optional)')
                                    ->url(),
                            ]),
                        ]),
                        
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            FileUpload::make('gallery')
                                ->label('Room Images')
                                ->image()
                                ->multiple()
                                ->panelLayout('grid'),
                        ]),
                        

                    Section::make('SEO')
                        ->schema([
                            TextInput::make('meta_title')
                                ->label('Meta Title'),
                            Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->rows(3),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    // Mock data removed — all data is now loaded from the database
    public static function getMockRoomTypes(): array
    {
        return [
            1 => [
                'id' => 1,
                'name' => 'Deluxe Room',
                'hotel' => 'Coral Beach Resort Sharjah',
                'room_type_id' => 'RM-DLX-001',
                'max_adults' => 2,
                'max_children' => 1,
                'room_size' => '35 sqm',
                'bed_type' => 'King Bed',
                'status' => 'Active',
            ],
            2 => [
                'id' => 2,
                'name' => 'Executive Room',
                'hotel' => 'Coral Dubai Deira Hotel',
                'room_type_id' => 'RM-EXE-002',
                'max_adults' => 2,
                'max_children' => 2,
                'room_size' => '42 sqm',
                'bed_type' => 'Twin Beds',
                'status' => 'Active',
            ],
            3 => [
                'id' => 3,
                'name' => 'Superior Room',
                'hotel' => 'ECOS Dubai Hotel',
                'room_type_id' => 'RM-SUP-003',
                'max_adults' => 2,
                'max_children' => 0,
                'room_size' => '28 sqm',
                'bed_type' => 'Queen Bed',
                'status' => 'Active',
            ],
            4 => [
                'id' => 4,
                'name' => 'Junior Suite',
                'hotel' => 'EWA Hotel Apartments',
                'room_type_id' => 'SU-JUN-004',
                'max_adults' => 3,
                'max_children' => 1,
                'room_size' => '55 sqm',
                'bed_type' => 'King Bed',
                'status' => 'Inactive',
            ],
            5 => [
                'id' => 5,
                'name' => 'Family Room',
                'hotel' => 'Opera Hotel',
                'room_type_id' => 'RM-FAM-005',
                'max_adults' => 4,
                'max_children' => 2,
                'room_size' => '65 sqm',
                'bed_type' => '2 Queen Beds',
                'status' => 'Active',
            ],
            6 => [
                'id' => 6,
                'name' => 'Presidential Suite',
                'hotel' => 'Coral Beach Resort Sharjah',
                'room_type_id' => 'SU-PRE-006',
                'max_adults' => 4,
                'max_children' => 0,
                'room_size' => '120 sqm',
                'bed_type' => 'King Bed',
                'status' => 'Active',
            ],
        ];
    }
}
