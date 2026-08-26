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
        $query = \App\Models\DiningOutlet::where('property_id', $this->record)
            ->orderBy('sort_order')
            ->orderBy('id');

        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        $outlets     = $query->forPage($currentPage, $this->perPage)->get()->map(function ($o) {
            $propName = $o->property ? (is_array($o->property->name) ? ($o->property->name['en'] ?? '') : $o->property->name) : '';
            return [
                'id' => $o->id,
                'name' => is_array($o->name) ? ($o->name['en'] ?? '') : $o->name,
                'hotel' => $propName,
                'cuisine_type' => is_array($o->cuisine_type) ? ($o->cuisine_type['en'] ?? '') : $o->cuisine_type,
                'opening_hours' => is_array($o->opening_hours) ? ($o->opening_hours['en'] ?? '') : $o->opening_hours,
                'table_booking' => $o->has_table_booking,
                'status' => $o->is_active ? 'Active' : 'Inactive',
                'image' => $o->getFirstMediaUrl('featured_image'),
            ];
        });
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
            ->fillForm(function (array $arguments) {
                $d = \App\Models\DiningOutlet::find($arguments['id']);
                if (!$d) return [];
                return [
                    'name' => is_array($d->name) ? ($d->name['en'] ?? '') : $d->name,
                    'slug' => $d->slug,
                    'cuisine_type' => is_array($d->cuisine_type) ? ($d->cuisine_type['en'] ?? '') : $d->cuisine_type,
                    'status' => $d->is_active ? 'active' : 'inactive',
                ];
            })
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
            ->fillForm(function (array $arguments) {
                $d = \App\Models\DiningOutlet::find($arguments['id']);
                if (!$d) return [];
                return [
                    'name' => is_array($d->name) ? ($d->name['en'] ?? '') : $d->name,
                    'slug' => $d->slug,
                    'cuisine_type' => is_array($d->cuisine_type) ? ($d->cuisine_type['en'] ?? '') : $d->cuisine_type,
                    'status' => $d->is_active ? 'active' : 'inactive',
                ];
            })
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\DiningOutlets\EditDiningOutlet::getUrl(['record' => $this->record, 'outlet_id' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function deleteDiningOutletAction(): Action
    {
        return Action::make('deleteDiningOutlet')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                \App\Models\DiningOutlet::find($arguments['id'])?->delete();
                Notification::make()
                    ->title('Dining Outlet Deleted')
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
                                ->options(fn () => \App\Models\Property::where('type', 'hotel')->get()->mapWithKeys(fn ($h) => [$h->id => is_array($h->name) ? ($h->name['en'] ?? '') : $h->name])->toArray())
                                ->default(fn () => request()->route('record') ?? null)
                                ->disabled()
                                ->dehydrated()
                                ->required(),
                            TextInput::make('name')
                                ->label('Outlet Name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required(),
                            TextInput::make('cuisine_type')
                                ->label('Cuisine Type')
                                ->required(),
                            TextInput::make('opening_hours')
                                ->label('Opening Hours')
                                ->placeholder('e.g. 10:00 AM - 11:00 PM'),
                            Toggle::make('has_table_booking')
                                ->label('Has Table Booking')
                                ->default(false),
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
                        
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('featured_image')
                                ->collection('featured_image')
                                ->label('Image Upload')
                                ->image(),
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
