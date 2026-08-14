<?php
namespace App\Filament\Pages\Hotels\RoomTypes;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;

class ManageRoomDetails extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/room-types/{room_id}/details';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    protected ?string $heading = 'Manage Room Details';

    public $record;
    public $room_id;
    public ?array $data = [];

    public function mount($record, $room_id): void
    {
        $this->record = $record;
        $this->room_id = $room_id;
        
        $mockData = \App\Filament\Pages\Hotels\RoomTypes\ListRoomTypes::getMockRoomTypes();
        $roomData = $mockData[$this->room_id] ?? [];
        
        $this->form->fill([
            'name' => $roomData['name'] ?? '',
            'room_area' => $roomData['room_size'] ?? '',
            'book_now_label' => 'BOOK NOW',
            'book_now_link' => '',
        ]);
        
        $this->form->fill($this->data);
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    public function form($form)
    {
        return $form->schema([
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Base Information')
                        ->description('Pre-filled from Room Type, but editable for the details page.')
                        ->schema([
                            TextInput::make('name')
                                ->label('Room Title')
                                ->required(),
                            \App\Filament\Forms\Components\JoditEditor::make('description')
                                ->label('Description'),
                        ]),
                        
                    Section::make('Room Specifications')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('starting_price')
                                    ->label('Starting Price')
                                    ->placeholder('e.g., From AED 299.00'),
                                TextInput::make('room_area')
                                    ->label('Room Area')
                                    ->placeholder('e.g., 27 m²'),
                            ]),
                        ]),

                    Section::make('Call to Action')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('book_now_label')
                                    ->label('Book Now Label')
                                    ->default('BOOK NOW'),
                                TextInput::make('book_now_link')
                                    ->label('Book Now Link (URL)'),
                            ]),
                        ]),
                        
                    Section::make('Special Features')
                        ->schema([
                            Repeater::make('special_features')
                                ->label('Features')
                                ->schema([
                                    TextInput::make('icon')
                                        ->label('Icon Name (e.g. heroicon-o-wifi)'),
                                    TextInput::make('feature_name')
                                        ->label('Feature Name')
                                        ->required(),
                                ])
                                ->columns(2)
                                ->defaultItems(1)
                                ->collapsible()
                                ->cloneable(),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media & Gallery')
                        ->schema([
                            FileUpload::make('featured_image')
                                ->label('Featured Image (Primary Banner)')
                                ->image()
                                ->helperText('Pre-filled from Room Type by default.'),
                            FileUpload::make('additional_gallery')
                                ->label('Additional Room Gallery')
                                ->image()
                                ->multiple()
                                ->panelLayout('grid')
                                ->helperText('Additional images specific to the details page.'),
                        ]),
                ])->columnSpan(1),
            ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Room Details Saved')->success()->send();
    }

    public function getBackUrl(): string 
    { 
        return \App\Filament\Pages\Hotels\RoomTypes\EditRoomType::getUrl(['record' => $this->record, 'room_id' => $this->room_id]); 
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
