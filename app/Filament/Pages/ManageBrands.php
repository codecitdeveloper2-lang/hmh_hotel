<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class ManageBrands extends Page
{
    protected string $view = 'filament.pages.manage-brands';


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
        $query = \App\Models\Property::where('type', 'brand');
        
        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        
        $brands = $query->skip(($currentPage - 1) * $this->perPage)
                        ->take($this->perPage)
                        ->get()
                        ->map(function ($brand) {
                            return [
                                'id' => $brand->id,
                                'name' => $brand->display_name,
                                'slug' => $brand->slug,
                                'star_segment' => $brand->star_rating ? $brand->star_rating . ' Star' : 'N/A',
                                'status' => $brand->is_active,
                                'sort_order' => $brand->sort_order,
                                'last_updated' => $brand->updated_at?->format('Y-m-d') ?? '',
                                'logo' => $brand->logo,
                            ];
                        });
                        
        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'brands', 'from', 'to');
    }

        public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-building-storefront';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Brand Management';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Brand Management';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Brand Management';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage all hotel brands within the HMH Hotel Group.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addBrand')
                ->label('Add Brand')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getBrandFormSchema())
                
            ->url(\App\Filament\Pages\Brands\CreateBrand::getUrl())
            ->action(function (array $data) {
                    $data['type'] = 'brand';
                    \App\Models\Property::create($data);
                    Notification::make()
                        ->title('Brand Created')
                        ->body('The brand has been created successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewBrandAction(): Action
    {
        return Action::make('viewBrand')
            ->modalHeading('View Brand')
            ->modalWidth('7xl')
            ->form($this->getBrandFormSchema())
            ->fillForm(fn (array $arguments) => \App\Models\Property::find($arguments['id'])?->toArray() ?? [])
            ->disabledForm()
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public function editBrandAction(): Action
    {
        return Action::make('editBrand')
            ->modalHeading('Edit Brand')
            ->modalWidth('7xl')
            ->form($this->getBrandFormSchema())
            ->fillForm(fn (array $arguments) => \App\Models\Property::find($arguments['id'])?->toArray() ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Brands\EditBrand::getUrl(['record' => $arguments['id'] ?? 0]))
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Brands\ViewBrand::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data, array $arguments) {
                \App\Models\Property::find($arguments['id'])?->update($data);
                Notification::make()
                    ->title('Brand Updated')
                    ->body('The brand has been updated successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteBrandAction(): Action
    {
        return Action::make('deleteBrand')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                \App\Models\Property::find($arguments['id'])?->delete();
                Notification::make()
                    ->title('Brand Deleted')
                    ->body('The brand has been deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public static function getBrandFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    \Filament\Schemas\Components\Tabs::make('Brand Tabs')->tabs([
                        \Filament\Schemas\Components\Tabs\Tab::make('General Details')->schema([
                            Section::make('General')->schema([
                                TextInput::make('name.en')
                                    ->required()
                                    ->dehydrated()
                                    ->label('Brand Name')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (?string $state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug($state ?? ''))),
                                TextInput::make('slug')->required()->label('Slug'),
                                TextInput::make('tagline')->label('Tagline'),
                                TextInput::make('google_location')->label('Google Location URL')->url(),
                                TextInput::make('location_title')->label('Location Title (e.g. WHERE WE ARE)'),
                                TextInput::make('contact_button_text')->label('Contact Button Text (e.g. CONTACT US)'),
                                TextInput::make('contact_button_url')->label('Contact Button URL')->url(),
                                Select::make('star_segment')
                                    ->options([
                                        'Economy' => 'Economy',
                                        'Midscale' => 'Midscale',
                                        'Premium' => 'Premium',
                                        'Luxury' => 'Luxury',
                                    ])
                                    ->required()
                                    ->label('Star Segment'),
                                FileUpload::make('logo')->label('Brand Logo (Placeholder)')->image()->disk('uploads'),
                            ]),
                            Section::make('Brand Intro')->schema([
                                TextInput::make('intro_subtitle.en')
                                    ->label('Intro Subtitle (e.g. Urban Comfort)')
                                    ->dehydrated(),
                                TextInput::make('intro_title.en')
                                    ->label('Intro Title (e.g. Welcome to Corp Hotels)')
                                    ->dehydrated(),
                                \App\Filament\Forms\Components\JoditEditor::make('intro_text.en')
                                    ->label('Intro Text')
                                    ->dehydrated()
                                    ->columnSpanFull(),
                            ]),
                        ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Banner')->schema([
                            Section::make('Banner')->schema([
                                TextInput::make('banner_title')->label('Banner Title'),
                                FileUpload::make('banner_images')->label('Banner Images')->image()->multiple()->disk('uploads')->columnSpanFull(),
                            ]),
                        ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Content Sections')->schema([
                            Section::make('Experience / Offers Section')->schema([
                                TextInput::make('brand_content.experience_section_title')
                                    ->label('Section Title (e.g. EXPERIENCE BAHI HOTELS)')
                                    ->placeholder('EXPERIENCE [BRAND NAME]'),
                                // Offer cards are pulled automatically from linked Offers
                            ]),
                            Section::make('Our Hotels Section')->schema([
                                TextInput::make('brand_content.our_hotels_section_title')
                                    ->label('Section Title (e.g. OUR HOTELS)')
                                    ->placeholder('OUR HOTELS'),
                                TextInput::make('brand_content.our_hotels_cta_link')
                                    ->label('Discover More Link URL (optional)')
                                    ->url()
                                    ->placeholder('https://...'),
                                // Hotel cards are pulled automatically from child Hotels
                            ]),
                            Section::make('Destinations / Map Section')->schema([
                                TextInput::make('brand_content.destinations_section_title')
                                    ->label('Section Title (e.g. BAHI HOTELS LOCATIONS)')
                                    ->placeholder('[BRAND NAME] LOCATIONS'),
                                TextInput::make('brand_content.destinations_cta_link')
                                    ->label('Discover Destinations Link URL (optional)')
                                    ->url()
                                    ->placeholder('https://...'),
                            ]),
                            Section::make('Our Brands Section')->schema([
                                TextInput::make('brand_content.our_brands_section_title')->label('Section Title (e.g. Our Brands)'),
                                \Filament\Forms\Components\Repeater::make('brand_content.brands_list')
                                    ->label('Brands List')
                                    ->schema([
                                        FileUpload::make('brand_logo')->label('Brand Logo')->image()->disk('uploads'),
                                        TextInput::make('brand_link')->label('Brand URL')->url(),
                                    ])
                                    ->grid(2)->columnSpanFull()
                            ]),
                        ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Footer')->schema([
                            Section::make('Footer Logos')->schema([
                                FileUpload::make('brand_content.footer_company_logo')->label('Company Logo')->image()->disk('uploads'),
                                FileUpload::make('brand_content.footer_brand_logo')->label('Brand Logo')->image()->disk('uploads'),
                            ])->columns(2),
                            Section::make('Value Proposition')->schema([
                                TextInput::make('brand_content.footer_value_prop_title')->label('Title'),
                                TextInput::make('brand_content.footer_value_prop_subtitle')->label('Subtitle'),
                                TextInput::make('brand_content.footer_value_prop_phone')->label('Phone Number'),
                            ])->columns(3),
                            Section::make('Newsletter & Socials')->schema([
                                TextInput::make('brand_content.footer_newsletter_title')->label('Newsletter Title'),
                                TextInput::make('brand_content.footer_social_title')->label('Social Title'),
                                \Filament\Forms\Components\Repeater::make('brand_content.footer_social_links')
                                    ->label('Social Links')
                                    ->schema([
                                        Select::make('platform')->options([
                                            'facebook' => 'Facebook',
                                            'instagram' => 'Instagram',
                                            'twitter' => 'Twitter/X',
                                            'linkedin' => 'LinkedIn',
                                            'youtube' => 'YouTube',
                                            'threads' => 'Threads',
                                        ])->required(),
                                        TextInput::make('url')->url()->required(),
                                    ])->columns(2)->columnSpanFull()
                            ])->columns(2),
                            Section::make('Footer Navigation')->schema([
                                \Filament\Forms\Components\Repeater::make('brand_content.footer_columns')
                                    ->label('Footer Columns')
                                    ->schema([
                                        TextInput::make('column_title')->label('Column Title')->required(),
                                        \Filament\Forms\Components\Repeater::make('links')
                                            ->label('Links')
                                            ->schema([
                                                TextInput::make('label')->required(),
                                                TextInput::make('url')->url()->required(),
                                            ])->columns(2)
                                    ])->columnSpanFull()
                            ]),
                            Section::make('Copyright')->schema([
                                Textarea::make('brand_content.footer_copyright_text')->label('Copyright Text')->columnSpanFull(),
                            ]),
                        ]),
                    ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('SEO')->schema([
                        TextInput::make('meta_title')->label('Meta Title'),
                        Textarea::make('meta_description')->label('Meta Description'),
                    ]),
                    Section::make('Settings')->schema([
                        TextInput::make('sort_order')->numeric()->default(0)->label('Sort Order'),
                        Toggle::make('status')->label('Status (Active)')->default(true)->inline(false),
                    ]),
                ])->columnSpan(1),
            ]),
        ];
    }
    // Mock Data removed
}


