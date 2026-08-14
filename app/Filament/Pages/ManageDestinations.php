<?php

namespace App\Filament\Pages;

use App\Models\Destination;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class ManageDestinations extends Page
{
    protected string $view = 'filament.pages.manage-destinations';

    public $searchQuery = '';
    public $filterCountry = '';
    public $filterStatus = '';


    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void
    {
        $this->currentPage = 1;
    }
    public function updatedFilterCountry(): void
    {
        $this->currentPage = 1;
    }
    public function updatedFilterStatus(): void
    {
        $this->currentPage = 1;
    }
    public function updatedPerPage(): void
    {
        $this->currentPage = 1;
    }

    public function nextPage(int $lastPage): void
    {
        if ($this->currentPage < $lastPage)
            $this->currentPage++;
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1)
            $this->currentPage--;
    }

    public function gotoPage(int $page): void
    {
        $this->currentPage = $page;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-map-pin';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Destination Management';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Destination Management';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Destination Management';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Manage destinations where HMH Hotel Group hotels are located.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addDestination')
                ->label('Add Destination')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getDestinationFormSchema())

                ->url(\App\Filament\Pages\Destinations\CreateDestination::getUrl())
                ->action(function (array $data) {
                    Notification::make()
                        ->title('Destination saved successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewDestinationAction(): Action
    {
        return Action::make('viewDestination')
            ->modalHeading('View Destination')
            ->modalWidth('7xl')
            ->form($this->getDestinationFormSchema())
            ->fillForm(fn(array $arguments) => self::getDestinationFormData($arguments['id'] ?? null))
            ->disabledForm()

            ->url(fn(array $arguments) => \App\Filament\Pages\Destinations\ViewDestination::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(fn() => null);
    }

    public function editDestinationAction(): Action
    {
        return Action::make('editDestination')
            ->modalHeading('Edit Destination')
            ->modalWidth('7xl')
            ->form($this->getDestinationFormSchema())
            ->fillForm(fn(array $arguments) => self::getDestinationFormData($arguments['id'] ?? null))

            ->url(fn(array $arguments) => \App\Filament\Pages\Destinations\EditDestination::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Destination saved successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteDestinationAction(): Action
    {
        return Action::make('deleteDestination')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                Destination::find($arguments['id'] ?? null)?->delete();

                Notification::make()
                    ->title('Destination deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public function getDestinationRows(): Collection
    {
        return self::getDatabaseDestinations();
    }

    public function getCountryOptions(): array
    {
        return Destination::query()
            ->whereNotNull('country')
            ->where('country', '<>', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->all();
    }

    public static function getDatabaseDestinations(): Collection
    {
        return Destination::query()
            ->with(['cities', 'media'])
            ->orderBy('sort_order')
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (Destination $destination): array {
                $firstBanner = $destination->getMedia('banner_images')
                    ->sortBy('order_column')
                    ->first();
                $firstBannerUrl = $firstBanner?->getUrl();

                // Handle both absolute (http://...) and relative (/storage/...) URLs
                if ($firstBannerUrl) {
                    $bannerPath = str_starts_with($firstBannerUrl, 'http')
                        ? parse_url($firstBannerUrl, PHP_URL_PATH)
                        : $firstBannerUrl;
                } else {
                    $bannerPath = null;
                }

                return [
                    'id' => $destination->id,
                    'name' => self::getEnglishValue($destination, 'name') ?: 'Untitled destination',
                    'slug' => $destination->slug,
                    'country' => $destination->country ?: 'N/A',
                    'hotels_count' => self::countHotelLabels($destination),
                    'status' => $destination->is_active ? 'Active' : 'Inactive',
                    'display_order' => $destination->sort_order,
                    'last_updated' => optional($destination->updated_at)->format('Y-m-d') ?: 'N/A',
                    'updated_at_timestamp' => optional($destination->updated_at)->timestamp ?? 0,
                    'banner_url' => $bannerPath,
                    'description' => self::getEnglishValue($destination, 'description'),
                ];
            });
    }

    public static function getDestinationFormData(int|string|null $record): array
    {
        if (blank($record)) {
            return [];
        }

        $destination = Destination::query()
            ->with(['cities', 'seoMetadata', 'media'])
            ->find($record);

        if (!$destination) {
            return [];
        }

        return [
            'activeLocale' => 'en',
            'id' => $destination->id,
            'name' => $destination->getTranslations('name'),
            'slug' => $destination->slug,
            'country' => $destination->country,
            'description' => $destination->getTranslations('description'),
            'status' => $destination->is_active ? 'Active' : 'Inactive',
            'display_order' => $destination->sort_order,
            'meta_title' => $destination->seoMetadata?->meta_title,
            'meta_description' => $destination->seoMetadata?->meta_description,
            'meta_keywords' => $destination->seoMetadata?->meta_keywords,
            'map_embed_code' => $destination->map_embed_code,
            'cities' => $destination->cities
                ->sortBy('sort_order')
                ->map(fn($city): array => [
                    'id' => $city->id,
                    'city_name' => $city->getTranslations('name'),
                    'description' => $city->getTranslations('description'),
                    'city_image' => $city->city_image,
                    'city_link' => $city->city_link,
                    'layout_type' => $city->layout_type,
                    'sort_order' => $city->sort_order,
                    'status' => $city->is_active ? 'Active' : 'Inactive',
                    'hotel_labels' => $city->hotel_labels ?? [],
                ])
                ->values()
                ->all(),
        ];
    }

    public static function getDestinationFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->extraAttributes(['style' => 'position: relative;'])
                        ->schema([
                            \Filament\Forms\Components\ToggleButtons::make('activeLocale')
                                ->hiddenLabel()
                                ->options([
                                    'en' => 'EN',
                                    'ar' => 'عربي',
                                ])
                                ->default('en')
                                ->grouped()
                                ->live()
                                ->extraFieldWrapperAttributes([
                                    'style' => 'position: absolute; top: 1rem; right: 1.5rem; width: max-content; margin: 0; z-index: 10;'
                                ]),

                            TextInput::make('name.en')
                                ->label('Destination Name')
                                ->required(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') === 'en')
                                ->hidden(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') !== 'en')
                                ->dehydratedWhenHidden()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(string $operation, $state, $set) => $set('slug', Str::slug($state))),
                            TextInput::make('name.ar')
                                ->label(new \Illuminate\Support\HtmlString('<div dir="rtl" style="text-align: right; width: 100%; display: block;">Destination Name (AR)<sup class="text-danger-600 font-medium" style="color: rgb(220 38 38); margin-right: 0.25rem;">*</sup></div>'))
                                ->markAsRequired(false)
                                ->required(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') === 'ar')
                                ->hidden(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') !== 'ar')
                                ->dehydratedWhenHidden()
                                ->extraInputAttributes(['dir' => 'rtl', 'style' => 'text-align: right;']),
                                ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required(),
                            Select::make('country')
                                ->label('Country')
                                ->options([
                                    'United Arab Emirates' => 'United Arab Emirates',
                                    'Saudi Arabia' => 'Saudi Arabia',
                                    'Bahrain' => 'Bahrain',
                                    'Oman' => 'Oman',
                                    'Qatar' => 'Qatar',
                                ])
                                ->required(),
                            \App\Filament\Forms\Components\JoditEditor::make('description.en')
                                ->label('Description')
                                ->hidden(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') !== 'en')
                                ->dehydratedWhenHidden(),
                            \App\Filament\Forms\Components\JoditEditor::make('description.ar')
                                ->label(new \Illuminate\Support\HtmlString('<div dir="rtl" style="text-align: right; width: 100%; display: block;">Description (AR)</div>'))
                                ->direction('rtl')
                                ->hidden(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') !== 'ar')
                                ->dehydratedWhenHidden(),
                            Grid::make(2)->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'Active' => 'Active',
                                        'Inactive' => 'Inactive',
                                    ])
                                    ->default('Active')
                                    ->required(),
                                TextInput::make('display_order')
                                    ->label('Display Order')
                                    ->rules(['integer', 'min:0'])
                                    ->extraInputAttributes([
                                        'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                                        'inputmode' => 'numeric',
                                    ])
                                    ->default(0),
                            ]),
                        ]),

                    Section::make('Cities')
                        ->schema([
                            \Filament\Forms\Components\Repeater::make('cities')
                                ->label('')
                                ->addActionLabel('Add City')
                                ->schema([
                                    Hidden::make('id'),
                                    TextInput::make('city_name.en')
                                        ->label('City Name')
                                        ->required(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') === 'en')
                                        ->hidden(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') !== 'en')
                                        ->dehydratedWhenHidden(),
                                    TextInput::make('city_name.ar')
                                        ->label(new \Illuminate\Support\HtmlString('<div dir="rtl" style="text-align: right; width: 100%; display: block;">City Name (AR)<sup class="text-danger-600 font-medium" style="color: rgb(220 38 38); margin-right: 0.25rem;">*</sup></div>'))
                                        ->markAsRequired(false)
                                        ->required(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') === 'ar')
                                        ->hidden(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') !== 'ar')
                                        ->dehydratedWhenHidden()
                                        ->extraInputAttributes(['dir' => 'rtl', 'style' => 'text-align: right;']),
                                    \App\Filament\Forms\Components\JoditEditor::make('description.en')
                                        ->label('Description')
                                        ->hidden(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') !== 'en')
                                        ->dehydratedWhenHidden(),
                                    \App\Filament\Forms\Components\JoditEditor::make('description.ar')
                                        ->label(new \Illuminate\Support\HtmlString('<div dir="rtl" style="text-align: right; width: 100%; display: block;">Description (AR)</div>'))
                                        ->direction('rtl')
                                        ->hidden(fn(\Livewire\Component $livewire) => ($livewire->data['activeLocale'] ?? 'en') !== 'ar')
                                        ->dehydratedWhenHidden(),
                                    FileUpload::make('city_image')
                                        ->label('City Image')
                                        ->image()
                                        ->disk('public')
                                        ->directory('city-images'),
                                    // TextInput::make('city_link')
                                    //     ->label('City Link')
                                    //     ->url(),
                                    Select::make('layout_type')
                                        ->label('Layout Type')
                                        ->options([
                                            'image_left' => 'Image Left',
                                            'image_right' => 'Image Right',
                                        ])
                                        ->default('image_left'),
                                    TextInput::make('sort_order')
                                        ->label('Sort Order')
                                        ->rules(['integer', 'min:0'])
                                        ->extraInputAttributes([
                                            'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                                            'inputmode' => 'numeric',
                                        ])
                                        ->default(0),
                                    Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'Active' => 'Active',
                                            'Inactive' => 'Inactive',
                                        ])
                                        ->default('Active'),

                                    \Filament\Forms\Components\Repeater::make('hotel_labels')
                                        ->label('Hotel Labels')
                                        ->addActionLabel('Add Hotel Label')
                                        ->schema([
                                            TextInput::make('hotel_name')
                                                ->label('Hotel Name')
                                                ->required(),
                                            TextInput::make('x_position')
                                                ->label('X Position (%)')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(100),
                                            TextInput::make('y_position')
                                                ->label('Y Position (%)')
                                                ->numeric()
                                                ->minValue(0)
                                                ->maxValue(100),
                                            TextInput::make('hotel_url')
                                                ->label('Hotel URL')
                                                ->url(),
                                        ])
                                        ->columns(2)
                                        ->collapsible()
                                ])
                                ->collapsible()
                                ->itemLabel(fn(array $state): ?string => $state['city_name']['en'] ?? null),
                        ]),
                ])->columnSpan(2),

                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('banner_image')
                                ->collection('banner_images')
                                ->label('Banner Image Upload (Multiple Images)')
                                ->image()
                                ->multiple()
                                ->panelLayout('grid'),
                            // FileUpload::make('thumbnail_image')
                            //     ->label('Thumbnail Image Upload')
                            //     ->image(),
                        ]),

                    Section::make('SEO')
                        ->schema([
                            TextInput::make('meta_title')
                                ->label('Meta Title'),
                            Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->rows(2),
                            TextInput::make('meta_keywords')
                                ->label('Meta Keywords'),
                        ]),
                        
                    Section::make('Hotel Links')
                        ->schema([
                            \Filament\Forms\Components\Repeater::make('map_embed_code')
                                ->label('Add Google Map Embed Code')
                                ->addActionLabel('Add Map Embed')
                                ->schema([
                                    Textarea::make('embed_code')
                                        ->label('Google Map Embed Code')
                                        ->rows(3)
                                        ->placeholder('<iframe src="https://www.google.com/maps/embed?..."></iframe>')
                                        ->required(),
                                ]),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    public static function getMockDestinations(): array
    {
        return self::getDatabaseDestinations()->keyBy('id')->toArray();
    }

    protected static function countHotelLabels(Destination $destination): int
    {
        return $destination->cities->sum(function ($city): int {
            return collect($city->hotel_labels ?? [])
                ->filter(fn($label): bool => filled($label['hotel_name'] ?? null))
                ->count();
        });
    }

    protected static function getEnglishValue($model, string $attribute): string
    {
        $translations = [];

        if (method_exists($model, 'getTranslations')) {
            $translations = $model->getTranslations($attribute);
        }

        if (filled($translations['en'] ?? null)) {
            return (string) $translations['en'];
        }

        $rawValue = $model->getRawOriginal($attribute);

        if (is_string($rawValue)) {
            $decoded = json_decode($rawValue, true);
            if (is_array($decoded)) {
                return (string) ($decoded['en'] ?? '');
            }
        }

        return '';
    }
}
