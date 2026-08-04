<?php

namespace App\Filament\Pages\Hotels\Attractions;

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

class ListAttractions extends Page
{
    use HasHotelTabs;

    protected string $view = 'filament.pages.manage-attractions';

    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;
    protected static ?string $slug = 'manage-hotels/{record}/attractions';
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
        $all         = collect($this->getMockAttractions())->filter(fn($item) => $item['hotel'] === $hotelName);
        $totalItems  = $all->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        $attractions = $all->forPage($currentPage, $this->perPage);
        $from        = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to          = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'attractions', 'from', 'to');
    }



    public function getTitle(): string | Htmlable
    {
        return 'Attractions';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Attractions';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage nearby attractions displayed for each hotel.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addAttraction')
                ->label('Add Attraction')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getAttractionFormSchema())
                
            ->url(fn () => \App\Filament\Pages\Hotels\Attractions\CreateAttraction::getUrl(['record' => $this->record]))
            ->action(function (array $data) {
                    Notification::make()
                        ->title('Attraction Created')
                        ->body('The attraction has been created successfully (Mock).')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewAttractionAction(): Action
    {
        return Action::make('viewAttraction')
            ->modalHeading('View Attraction')
            ->modalWidth('7xl')
            ->form($this->getAttractionFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockAttractions()[$arguments['id']] ?? [])
            ->disabledForm()
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\Attractions\ViewAttraction::getUrl(['record' => $this->record, 'attraction_id' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editAttractionAction(): Action
    {
        return Action::make('editAttraction')
            ->modalHeading('Edit Attraction')
            ->modalWidth('7xl')
            ->form($this->getAttractionFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockAttractions()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\Attractions\EditAttraction::getUrl(['record' => $this->record, 'attraction_id' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Attraction Updated')
                    ->body('The attraction has been updated successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public function deleteAttractionAction(): Action
    {
        return Action::make('deleteAttraction')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Attraction Deleted')
                    ->body('The attraction has been deleted successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public static function getAttractionFormSchema(): array
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
                                ->label('Attraction Name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required(),
                            Select::make('category')
                                ->label('Category')
                                ->options([
                                    'shopping' => 'Shopping',
                                    'landmark' => 'Landmark',
                                    'beach' => 'Beach',
                                    'museum' => 'Museum & Culture',
                                    'family' => 'Family & Theme Park',
                                ])
                                ->required(),
                            Textarea::make('description')
                                ->label('Description')
                                ->rows(4),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                ])
                                ->default('active')
                                ->required(),
                        ]),
                        
                    Section::make('Location')
                        ->schema([
                            TextInput::make('distance')
                                ->label('Distance from Hotel (e.g. 5 km)'),
                            TextInput::make('maps_url')
                                ->label('Google Maps URL')
                                ->url(),
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

    public static function getMockAttractions(): array
    {
        return [
            1 => [
                'id' => 1,
                'name' => 'Burj Khalifa',
                'hotel' => 'Coral Dubai Deira Hotel',
                'distance' => '12 km',
                'category' => 'Landmark',
                'status' => 'Active',
                'last_updated' => '2023-10-15 09:30',
            ],
            2 => [
                'id' => 2,
                'name' => 'Dubai Mall',
                'hotel' => 'Coral Dubai Deira Hotel',
                'distance' => '11 km',
                'category' => 'Shopping',
                'status' => 'Active',
                'last_updated' => '2023-10-16 11:45',
            ],
            3 => [
                'id' => 3,
                'name' => 'Palm Jumeirah',
                'hotel' => 'Opera Hotel',
                'distance' => '25 km',
                'category' => 'Landmark',
                'status' => 'Active',
                'last_updated' => '2023-10-17 14:20',
            ],
            4 => [
                'id' => 4,
                'name' => 'Dubai Marina',
                'hotel' => 'ECOS Dubai Hotel',
                'distance' => '28 km',
                'category' => 'Landmark',
                'status' => 'Inactive',
                'last_updated' => '2023-10-18 10:15',
            ],
            5 => [
                'id' => 5,
                'name' => 'Sharjah Aquarium',
                'hotel' => 'Coral Beach Resort Sharjah',
                'distance' => '8 km',
                'category' => 'Museum & Culture',
                'status' => 'Active',
                'last_updated' => '2023-10-19 16:50',
            ],
            6 => [
                'id' => 6,
                'name' => 'Al Noor Island',
                'hotel' => 'Coral Beach Resort Sharjah',
                'distance' => '10 km',
                'category' => 'Family & Theme Park',
                'status' => 'Active',
                'last_updated' => '2023-10-20 08:15',
            ],
            7 => [
                'id' => 7,
                'name' => 'Museum of the Future',
                'hotel' => 'EWA Hotel Apartments',
                'distance' => '9 km',
                'category' => 'Museum & Culture',
                'status' => 'Active',
                'last_updated' => '2023-10-21 13:40',
            ],
            8 => [
                'id' => 8,
                'name' => 'Jumeirah Beach',
                'hotel' => 'Opera Hotel',
                'distance' => '15 km',
                'category' => 'Beach',
                'status' => 'Active',
                'last_updated' => '2023-10-22 17:10',
            ],
        ];
    }
}
