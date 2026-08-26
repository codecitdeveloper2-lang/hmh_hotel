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

    public string $activeLocale = 'en';

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
        $query = \App\Models\Property::where('type', 'hotel');
        
        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        
        $hotels = $query->with('parent')
                        ->skip(($currentPage - 1) * $this->perPage)
                        ->take($this->perPage)
                        ->get()
                        ->map(function ($hotel) {
                            $statusMap = [
                                'live' => 'Live',
                                'coming_soon' => 'Coming Soon',
                                'closed' => 'Closed',
                            ];
                            $firstBanner = is_array($hotel->banner_images) 
                                ? ($hotel->banner_images[array_key_first($hotel->banner_images)] ?? null) 
                                : (is_string($hotel->banner_images) ? (json_decode($hotel->banner_images, true)[0] ?? null) : null);
                            
                            $imageUrl = $firstBanner 
                                ? (str_starts_with($firstBanner, 'http') ? $firstBanner : url('uploads/' . ltrim($firstBanner, '/')))
                                : null;

                            return [
                                'id' => $hotel->id,
                                'name' => $hotel->display_name,
                                'brand' => $hotel->parent?->display_name ?? 'N/A',
                                'country' => $hotel->country ?? 'N/A',
                                'city' => $hotel->city ?? 'N/A',
                                'star_rating' => $hotel->star_rating ? $hotel->star_rating . ' Star' : 'N/A',
                                'status' => $statusMap[$hotel->status] ?? 'Live',
                                'last_updated' => $hotel->updated_at?->format('Y-m-d') ?? '',
                                'image_url' => $imageUrl,
                            ];
                        });
                        
        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

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
                    $data['type'] = 'hotel';
                    \App\Models\Property::create($data);
                    Notification::make()
                        ->title('Hotel Created')
                        ->body('The hotel has been created successfully.')
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
            ->fillForm(fn (array $arguments) => \App\Models\Property::find($arguments['id'])?->toArray() ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\EditHotel::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data, array $arguments) {
                \App\Models\Property::find($arguments['id'])?->update($data);
                Notification::make()
                    ->title('Hotel Updated')
                    ->body('The hotel has been updated successfully.')
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
            ->action(function (array $arguments) {
                \App\Models\Property::find($arguments['id'])?->delete();
                Notification::make()
                    ->title('Hotel Deleted')
                    ->body('The hotel has been deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public static function getHotelFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                \Filament\Schemas\Components\Tabs::make('Main Tabs')
                    ->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('General Information')
                            ->schema([
                                Section::make('Basic Information')
                                    ->schema([
                                        TextInput::make('name.en')
                                            ->label('Hotel Name')
                                            ->required()
                                            ->dehydrated()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug($state ?? ''))),
                                        Select::make('parent_id')
                                            ->label('Brand')
                                            ->options(fn () => \App\Models\Property::where('type', 'brand')->get()->mapWithKeys(fn ($b) => [$b->id => $b->display_name])->toArray())
                                            ->required(),
                                        TextInput::make('slug')
                                            ->label('Slug')
                                            ->required(),
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
                                                'coming_soon' => 'Coming Soon',
                                                'closed' => 'Closed',
                                            ])
                                            ->default('live')
                                            ->required(),
                                        Toggle::make('is_featured')
                                            ->label('Featured on Home Page')
                                            ->default(false),
                                    ]),
                                    
                                Section::make('Hotel Intro')
                                    ->schema([
                                        TextInput::make('intro_subtitle.en')
                                            ->label('Intro Subtitle (e.g. IMPECCABLY PLUSH)')
                                            ->dehydrated(),
                                        TextInput::make('intro_title.en')
                                            ->label('Intro Title (e.g. WELCOME TO BAHI AJMAN PALACE HOTEL)')
                                            ->dehydrated(),
                                        \App\Filament\Forms\Components\JoditEditor::make('description.en')
                                            ->label('Description (Intro Text)')
                                            ->dehydrated(),
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
                            ]),
                            
                        \Filament\Schemas\Components\Tabs\Tab::make('Banner')
                            ->schema([
                                Section::make('Banner Section')
                                    ->schema([
                                        FileUpload::make('banner_images')
                                            ->label('Banner Images')
                                            ->disk('uploads')
                                            ->directory('')
                                            ->multiple()
                                            ->image(),
                                        \Filament\Forms\Components\Repeater::make('banner_slides')
                                            ->label('Banner Slides (With Text/Buttons)')
                                            ->schema([
                                                FileUpload::make('image')
                                                    ->label('Background Image')
                                                    ->disk('uploads')
                                                    ->directory('')
                                                    ->image()
                                                    ->required(),
                                                TextInput::make('title')
                                                    ->label('Slide Title (Optional)'),
                                                TextInput::make('button_text')
                                                    ->label('Button Text (Optional)'),
                                                TextInput::make('button_link')
                                                    ->label('Button Link (Optional)')
                                                    ->url(),
                                            ])
                                            ->defaultItems(0)
                                            ->collapsible()
                                            ->cloneable()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Slide'),
                                    ]),
                            ]),
                    ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            FileUpload::make('cover_image')
                                ->label('Cover Image Upload')
                                ->disk('uploads')
                                ->directory('')
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
                            TextInput::make('meta_title.en')
                                ->label('Meta Title')
                                ->dehydrated(),
                            Textarea::make('meta_description.en')
                                ->label('Meta Description')
                                ->rows(3)
                                ->dehydrated(),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    // Mock Data removed
}
