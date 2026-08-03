<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class ManageHotels extends Page
{
    protected string $view = 'filament.pages.manage-hotels';

    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    protected static ?int $navigationSort = 1;


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
        $all         = collect($this->getMockHotels());
        $totalItems  = $all->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        $hotels      = $all->forPage($currentPage, $this->perPage);
        $from        = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to          = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'hotels', 'from', 'to');
    }

    public function getSubNavigation(): array
    {
        return [];
    }

        public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-building-office-2';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Hotels';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Hotel Management';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Hotel Management';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage all hotels within the HMH Hotel Group.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addHotel')
                ->label('Add Hotel')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getHotelFormSchema())
                
            ->url(\App\Filament\Pages\Hotels\CreateHotel::getUrl())
            ->action(function (array $data) {
                    Notification::make()
                        ->title('Hotel Created')
                        ->body('The hotel has been created successfully (Mock).')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewHotelAction(): Action
    {
        return Action::make('viewHotel')
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\Overview::getUrl(['record' => $arguments['id'] ?? 0]));
    }

    public function editHotelAction(): Action
    {
        return Action::make('editHotel')
            ->modalHeading('Edit Hotel')
            ->modalWidth('7xl')
            ->form($this->getHotelFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockHotels()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\EditHotel::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Hotel Updated')
                    ->body('The hotel has been updated successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public function deleteHotelAction(): Action
    {
        return Action::make('deleteHotel')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Hotel Deleted')
                    ->body('The hotel has been deleted successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public static function getHotelFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            TextInput::make('name')
                                ->label('Hotel Name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $set('slug', Str::slug($state))),
                            Select::make('brand')
                                ->label('Brand')
                                ->options([
                                    'coral-hotels-resorts' => 'Coral Hotels & Resorts',
                                    'ecos-hotels' => 'ECOS Hotels',
                                    'ewa-hotel-apartments' => 'EWA Hotel Apartments',
                                    'hmh-hotels' => 'HMH Hotels',
                                    'opera-hotel' => 'Opera Hotel',
                                ])
                                ->required(),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required(),
                            \App\Filament\Forms\Components\JoditEditor::make('description')
                                ->label('Description'),
                            Select::make('star_rating')
                                ->label('Star Rating')
                                ->options([
                                    '1' => '1 Star',
                                    '2' => '2 Stars',
                                    '3' => '3 Stars',
                                    '4' => '4 Stars',
                                    '5' => '5 Stars',
                                ])
                                ->required(),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'live' => 'Live',
                                    'coming-soon' => 'Coming Soon',
                                    'closed' => 'Closed',
                                ])
                                ->default('live')
                                ->required(),
                        ]),
                        
                    Section::make('Location')
                        ->schema([
                            Textarea::make('address')
                                ->label('Address')
                                ->rows(2)
                                ->required(),
                            Grid::make(2)->schema([
                                TextInput::make('country')
                                    ->label('Country')
                                    ->required(),
                                TextInput::make('city')
                                    ->label('City')
                                    ->required(),
                            ]),
                            Grid::make(2)->schema([
                                TextInput::make('latitude')
                                    ->label('Latitude'),
                                TextInput::make('longitude')
                                    ->label('Longitude'),
                            ]),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            FileUpload::make('cover_image')
                                ->label('Cover Image Upload')
                                ->image(),
                        ]),
                        
                    Section::make('Check-in / Check-out')
                        ->schema([
                            Grid::make(2)->schema([
                                TimePicker::make('check_in_time')
                                    ->label('Check-in Time'),
                                TimePicker::make('check_out_time')
                                    ->label('Check-out Time'),
                            ]),
                        ]),
                        
                    Section::make('Contact Information')
                        ->schema([
                            TextInput::make('phone')
                                ->label('Phone')
                                ->tel(),
                            TextInput::make('email')
                                ->label('Email')
                                ->email(),
                            TextInput::make('website')
                                ->label('Website')
                                ->url(),
                        ]),
                        
                    Section::make('Booking Information')
                        ->schema([
                            TextInput::make('travelclick_hotel_id')
                                ->label('TravelClick Hotel ID'),
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

    public static function getMockHotels(): array
    {
        return [
            1 => [
                'id' => 1,
                'name' => 'Coral Beach Resort Sharjah',
                'brand' => 'Coral Hotels & Resorts',
                'country' => 'United Arab Emirates',
                'city' => 'Sharjah',
                'star_rating' => '4 Stars',
                'status' => 'Live',
                'last_updated' => '2023-10-15 09:30',
            ],
            2 => [
                'id' => 2,
                'name' => 'Coral Dubai Deira Hotel',
                'brand' => 'Coral Hotels & Resorts',
                'country' => 'United Arab Emirates',
                'city' => 'Dubai',
                'star_rating' => '4 Stars',
                'status' => 'Live',
                'last_updated' => '2023-10-16 11:45',
            ],
            3 => [
                'id' => 3,
                'name' => 'ECOS Dubai Hotel',
                'brand' => 'ECOS Hotels',
                'country' => 'United Arab Emirates',
                'city' => 'Dubai',
                'star_rating' => '3 Stars',
                'status' => 'Live',
                'last_updated' => '2023-10-17 14:20',
            ],
            4 => [
                'id' => 4,
                'name' => 'EWA Hotel Apartments',
                'brand' => 'EWA Hotel Apartments',
                'country' => 'United Arab Emirates',
                'city' => 'Dubai',
                'star_rating' => 'Unrated',
                'status' => 'Coming Soon',
                'last_updated' => '2023-10-18 10:15',
            ],
            5 => [
                'id' => 5,
                'name' => 'Opera Hotel',
                'brand' => 'Opera Hotel',
                'country' => 'United Arab Emirates',
                'city' => 'Dubai',
                'star_rating' => '5 Stars',
                'status' => 'Closed',
                'last_updated' => '2023-10-19 16:50',
            ],
        ];
    }
}
