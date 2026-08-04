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
        $all         = collect($this->getMockBrands());
        $totalItems  = $all->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        $brands      = $all->forPage($currentPage, $this->perPage);
        $from        = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to          = min($currentPage * $this->perPage, $totalItems);

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
                    Notification::make()
                        ->title('Brand Created')
                        ->body('The brand has been created successfully (Mock).')
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
            ->fillForm(fn (array $arguments) => $this->getMockBrands()[$arguments['id']] ?? [])
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
            ->fillForm(fn (array $arguments) => $this->getMockBrands()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Brands\EditBrand::getUrl(['record' => $arguments['id'] ?? 0]))
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Brands\ViewBrand::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Brand Updated')
                    ->body('The brand has been updated successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public function deleteBrandAction(): Action
    {
        return Action::make('deleteBrand')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Brand Deleted')
                    ->body('The brand has been deleted successfully (Mock).')
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
                                TextInput::make('name')->required()->label('Brand Name')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (?string $state, \Filament\Forms\Set $set) => $set('slug', Str::slug($state ?? ''))),
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
                                FileUpload::make('logo')->label('Brand Logo (Placeholder)')->image(),
                            ]),
                            Section::make('Brand Intro')->schema([
                                TextInput::make('intro_subtitle')->label('Intro Subtitle (e.g. Urban Comfort)'),
                                TextInput::make('intro_title')->label('Intro Title (e.g. Welcome to Corp Hotels)'),
                                \App\Filament\Forms\Components\JoditEditor::make('intro_text')->label('Intro Text')->columnSpanFull(),
                            ]),
                        ]),
                        \Filament\Schemas\Components\Tabs\Tab::make('Banner')->schema([
                            Section::make('Banner')->schema([
                                TextInput::make('banner_title')->label('Banner Title'),
                                FileUpload::make('banner_images')->label('Banner Images')->image()->multiple()->columnSpanFull(),
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

    public static function getMockBrands(): array
    {
        return [
            0 => ['id' => 0, 'name' => 'Coral Hotels & Resorts', 'slug' => 'coral-hotels-resorts', 'star_segment' => 'Premium', 'status' => true, 'sort_order' => 1, 'last_updated' => '2023-10-01 10:30'],
            1 => ['id' => 1, 'name' => 'ECOS Hotels', 'slug' => 'ecos-hotels', 'star_segment' => 'Economy', 'status' => true, 'sort_order' => 2, 'last_updated' => '2023-10-02 11:15'],
            2 => ['id' => 2, 'name' => 'EWA Hotel Apartments', 'slug' => 'ewa-hotel-apartments', 'star_segment' => 'Midscale', 'status' => true, 'sort_order' => 3, 'last_updated' => '2023-10-03 09:45'],
            3 => ['id' => 3, 'name' => 'HMH Hotels', 'slug' => 'hmh-hotels', 'star_segment' => 'Luxury', 'status' => false, 'sort_order' => 4, 'last_updated' => '2023-10-04 14:20'],
            4 => ['id' => 4, 'name' => 'Opera Hotel', 'slug' => 'opera-hotel', 'star_segment' => 'Premium', 'status' => true, 'sort_order' => 5, 'last_updated' => '2023-10-05 16:00'],
        ];
    }
}
