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
        $query = \App\Models\Attraction::where('property_id', $this->record)
            ->orderBy('sort_order')
            ->orderBy('id');

        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        $attractions = $query->forPage($currentPage, $this->perPage)->get()->map(function ($a) {
            $propName = $a->property ? (is_array($a->property->name) ? ($a->property->name['en'] ?? '') : $a->property->name) : '';
            return [
                'id' => $a->id,
                'name' => is_array($a->name) ? ($a->name['en'] ?? '') : $a->name,
                'hotel' => $propName,
                'category' => 'Local Attraction',
                'description' => is_array($a->description) ? ($a->description['en'] ?? '') : $a->description,
                'status' => $a->is_active ? 'Active' : 'Inactive',
                'last_updated' => $a->updated_at ? $a->updated_at->format('M d, Y') : 'Today',
                'image' => ($u = $a->getFirstMediaUrl('featured_image')) ? parse_url($u, PHP_URL_PATH) : '',
            ];
        });
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
            ->fillForm(function (array $arguments) {
                $a = \App\Models\Attraction::find($arguments['id']);
                if (!$a) return [];
                return [
                    'name' => is_array($a->name) ? ($a->name['en'] ?? '') : $a->name,
                    'slug' => $a->slug,
                    'description' => is_array($a->description) ? ($a->description['en'] ?? '') : $a->description,
                    'status' => $a->is_active ? 'active' : 'inactive',
                ];
            })
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
            ->fillForm(function (array $arguments) {
                $a = \App\Models\Attraction::find($arguments['id']);
                if (!$a) return [];
                return [
                    'name' => is_array($a->name) ? ($a->name['en'] ?? '') : $a->name,
                    'slug' => $a->slug,
                    'description' => is_array($a->description) ? ($a->description['en'] ?? '') : $a->description,
                    'status' => $a->is_active ? 'active' : 'inactive',
                ];
            })
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\Attractions\EditAttraction::getUrl(['record' => $this->record, 'attraction_id' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function deleteAttractionAction(): Action
    {
        return Action::make('deleteAttraction')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                \App\Models\Attraction::find($arguments['id'])?->delete();
                Notification::make()
                    ->title('Attraction Deleted')
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
                                ->options(fn () => \App\Models\Property::where('type', 'hotel')->get()->mapWithKeys(fn ($h) => [$h->id => is_array($h->name) ? ($h->name['en'] ?? '') : $h->name])->toArray())
                                ->default(fn () => request()->route('record') ?? null)
                                ->disabled()
                                ->dehydrated()
                                ->required(),
                            TextInput::make('name')
                                ->label('Attraction Name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required(),
                            \App\Filament\Forms\Components\JoditEditor::make('description')
                                ->label('Description'),
                            TextInput::make('address')
                                ->label('Address'),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                ])
                                ->default('active')
                                ->required(),
                        ]),
                        
                    Section::make('Call to Action')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('read_more_label')
                                    ->label('Read More Label')
                                    ->default('READ MORE'),
                                TextInput::make('read_more_link')
                                    ->label('Read More Link')
                                    ->url(),
                            ]),
                        ]),
                    Section::make('Map Location')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric(),
                                TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric(),
                            ]),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('featured_image')
                                ->disk('uploads')
                                ->collection('featured_image')
                                ->label('Featured Image Upload')
                                ->image(),
                            \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('gallery')
                                ->disk('uploads')
                                ->collection('gallery')
                                ->label('Gallery Upload')
                                ->image()
                                ->multiple()
                                ->panelLayout('grid'),
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
