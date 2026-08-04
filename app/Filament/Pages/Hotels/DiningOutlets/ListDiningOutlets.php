<?php

namespace App\Filament\Pages\Hotels\DiningOutlets;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;

class ListDiningOutlets extends Page
{
    use HasHotelTabs;

    protected string $view = 'filament.pages.manage-dining-outlets';

    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;
    protected static ?string $slug = 'manage-hotels/{record}/dining-outlets';
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
        $hotelName = \App\Filament\Pages\ManageHotels::getMockHotels()[$this->record]['name'] ?? '';
        $all         = collect($this->getMockDiningOutlets())->filter(fn($item) => $item['hotel'] === $hotelName);
        $totalItems  = $all->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        $outlets     = $all->forPage($currentPage, $this->perPage);
        $from        = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to          = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'outlets', 'from', 'to');
    }



    public function getTitle(): string | Htmlable
    {
        return 'Dining Outlets';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Dining Outlets';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage restaurants, cafés, bars, and dining facilities available at each hotel.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addDiningOutlet')
                ->label('Add Dining Outlet')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getDiningOutletFormSchema())
                
            ->url(fn () => \App\Filament\Pages\Hotels\DiningOutlets\CreateDiningOutlet::getUrl(['record' => $this->record]))
            ->action(function (array $data) {
                    Notification::make()
                        ->title('Dining Outlet Created')
                        ->body('The dining outlet has been created successfully (Mock).')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewDiningOutletAction(): Action
    {
        return Action::make('viewDiningOutlet')
            ->modalHeading('View Dining Outlet')
            ->modalWidth('7xl')
            ->form($this->getDiningOutletFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockDiningOutlets()[$arguments['id']] ?? [])
            ->disabledForm()
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\DiningOutlets\ViewDiningOutlet::getUrl(['record' => $this->record, 'outlet_id' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editDiningOutletAction(): Action
    {
        return Action::make('editDiningOutlet')
            ->modalHeading('Edit Dining Outlet')
            ->modalWidth('7xl')
            ->form($this->getDiningOutletFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockDiningOutlets()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\DiningOutlets\EditDiningOutlet::getUrl(['record' => $this->record, 'outlet_id' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Dining Outlet Updated')
                    ->body('The dining outlet has been updated successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public function deleteDiningOutletAction(): Action
    {
        return Action::make('deleteDiningOutlet')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Dining Outlet Deleted')
                    ->body('The dining outlet has been deleted successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public static function getDiningOutletFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            Select::make('hotel')
                                ->label('Hotel')
                                ->options([
                                    '1' => 'Bahi Ajman Palace Hotel',
                                    'coral-beach-resort-sharjah' => 'Coral Beach Resort Sharjah',
                                    'coral-dubai-deira-hotel' => 'Coral Dubai Deira Hotel',
                                    'ecos-dubai-hotel' => 'ECOS Dubai Hotel',
                                    'ewa-hotel-apartments' => 'EWA Hotel Apartments',
                                    'opera-hotel' => 'Opera Hotel',
                                ])
                                ->default(fn () => request()->route('record') ?? '1')
                                ->disabled()
                                ->dehydrated()
                                ->required(),
                            TextInput::make('name')
                                ->label('Outlet Name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required(),
                            Textarea::make('description')
                                ->label('Description')
                                ->rows(4),
                            TextInput::make('cuisine_type')
                                ->label('Cuisine Type')
                                ->required(),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                ])
                                ->default('active')
                                ->required(),
                        ]),
                        
                    Section::make('Operating Information')
                        ->schema([
                            Grid::make(2)->schema([
                                TimePicker::make('opening_hours')
                                    ->label('Opening Hours'),
                                TimePicker::make('closing_hours')
                                    ->label('Closing Hours'),
                            ]),
                            Toggle::make('table_booking')
                                ->label('Table Booking Available')
                                ->default(false),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            FileUpload::make('featured_image')
                                ->label('Featured Image Upload')
                                ->image(),
                            FileUpload::make('gallery')
                                ->label('Gallery Upload (Placeholder)')
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

    public static function getMockDiningOutlets(): array
    {
        return [
            1 => [
                'id' => 1,
                'name' => 'Waves Restaurant',
                'hotel' => 'Coral Beach Resort Sharjah',
                'cuisine_type' => 'International Buffet',
                'opening_hours' => '06:30',
                'closing_hours' => '23:00',
                'table_booking' => true,
                'status' => 'Active',
            ],
            2 => [
                'id' => 2,
                'name' => 'Al Dente Italian',
                'hotel' => 'Coral Beach Resort Sharjah',
                'cuisine_type' => 'Italian',
                'opening_hours' => '12:00',
                'closing_hours' => '23:30',
                'table_booking' => true,
                'status' => 'Active',
            ],
            3 => [
                'id' => 3,
                'name' => 'Sky Lounge',
                'hotel' => 'Coral Dubai Deira Hotel',
                'cuisine_type' => 'Drinks & Snacks',
                'opening_hours' => '17:00',
                'closing_hours' => '02:00',
                'table_booking' => false,
                'status' => 'Active',
            ],
            4 => [
                'id' => 4,
                'name' => 'Spice Garden',
                'hotel' => 'ECOS Dubai Hotel',
                'cuisine_type' => 'Indian / Asian',
                'opening_hours' => '18:00',
                'closing_hours' => '23:00',
                'table_booking' => true,
                'status' => 'Active',
            ],
            5 => [
                'id' => 5,
                'name' => 'Ocean Grill',
                'hotel' => 'Opera Hotel',
                'cuisine_type' => 'Seafood & Steak',
                'opening_hours' => '19:00',
                'closing_hours' => '23:30',
                'table_booking' => true,
                'status' => 'Inactive',
            ],
            6 => [
                'id' => 6,
                'name' => 'Café Opera',
                'hotel' => 'Opera Hotel',
                'cuisine_type' => 'Café / Pastries',
                'opening_hours' => '07:00',
                'closing_hours' => '20:00',
                'table_booking' => false,
                'status' => 'Active',
            ],
        ];
    }
}
