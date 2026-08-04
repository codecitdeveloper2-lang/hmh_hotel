<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class ManageCmsPages extends Page
{
    protected string $view = 'filament.pages.manage-cms-pages';

    public $searchQuery = '';
    public $filterType = '';
    public $filterStatus = '';


    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterType(): void { $this->currentPage = 1; }
    public function updatedFilterStatus(): void { $this->currentPage = 1; }
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

        public static function getNavigationGroup(): ?string
    {
        return 'Content Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-document-text';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'CMS Pages';
    }

    public function getTitle(): string | Htmlable
    {
        return 'CMS Pages';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'CMS Pages';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage all website pages displayed across the HMH Hotel Group website.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addPage')
                ->label('Add Page')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getPageFormSchema())
                
            ->url(\App\Filament\Pages\CmsPages\CreateCmsPage::getUrl())
            ->action(function (array $data) {
                    Notification::make()
                        ->title('Page saved successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewPageAction(): Action
    {
        return Action::make('viewPage')
            ->modalHeading('View Page')
            ->modalWidth('7xl')
            ->form($this->getPageFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockPages()[$arguments['id']] ?? [])
            ->disabledForm()
            
            ->url(fn (array $arguments) => \App\Filament\Pages\CmsPages\ViewCmsPage::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editPageAction(): Action
    {
        return Action::make('editPage')
            ->modalHeading('Edit Page')
            ->modalWidth('7xl')
            ->form($this->getPageFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockPages()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\CmsPages\EditCmsPage::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Page saved successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deletePageAction(): Action
    {
        return Action::make('deletePage')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Page deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public static function getPageFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                    Grid::make(1)->schema([
                        Section::make('Basic Information')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('title')
                                        ->label('Page Title')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $set('slug', Str::slug($state))),
                                    TextInput::make('slug')
                                        ->label('URL Slug')
                                        ->required(),
                                ]),
                                Grid::make(3)->schema([
                                    Select::make('page_type')
                                        ->label('Page Type')
                                        ->options([
                                            'Standard' => 'Standard',
                                            'Landing Page' => 'Landing Page',
                                            'Legal' => 'Legal',
                                        ])
                                        ->required(),
                                    Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'Published' => 'Published',
                                            'Draft' => 'Draft',
                                        ])
                                        ->default('Published')
                                        ->required(),
                                    TextInput::make('display_order')
                                        ->label('Display Order')
                                        ->numeric(),
                                ]),
                            ]),

                        Section::make('Banner Section')
                            ->schema([
                                \Filament\Forms\Components\Repeater::make('banner_slides')
                                    ->label('Banner Slides')
                                    ->schema([
                                        FileUpload::make('image')
                                            ->label('Background Image')
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
                                    ->defaultItems(1)
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Slide'),
                            ]),
                            
                        Section::make('Content')
                            ->schema([
                                TextInput::make('content_title')
                                    ->label('Section Title (Optional)'),
                                \App\Filament\Forms\Components\JoditEditor::make('content')
                                    ->label('Rich Text Editor (Description)'),
                                Grid::make(2)->schema([
                                    TextInput::make('cta_text')
                                        ->label('Button Text'),
                                    TextInput::make('cta_link')
                                        ->label('Button Link')
                                        ->url(),
                                ]),
                            ]),
                            
                        \Filament\Schemas\Components\Group::make()
                            ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => $get('slug') === 'about-us')
                            ->schema([
                                Section::make('Intro Section')
                                    ->schema([
                                        TextInput::make('intro_subtitle')
                                            ->label('Intro Subtitle (e.g. Hospitality Management Holding)'),
                                        TextInput::make('intro_title')
                                            ->label('Intro Title (e.g. Get To Know)'),
                                        Textarea::make('intro_text')
                                            ->label('Intro Text')
                                            ->rows(4),
                                    ]),
                                Section::make('Expansion Section')
                                    ->schema([
                                        FileUpload::make('expansion_image')
                                            ->label('Side Image')
                                            ->image(),
                                        \App\Filament\Forms\Components\JoditEditor::make('expansion_text')
                                            ->label('Expansion Text'),
                                    ]),
                                Section::make('Our Vision')
                                    ->schema([
                                        Textarea::make('our_vision_text')
                                            ->label('Vision Text')
                                            ->rows(4),
                                        FileUpload::make('our_vision_image')
                                            ->label('Vision Image')
                                            ->image(),
                                    ]),
                                Section::make('Our Mission')
                                    ->schema([
                                        Textarea::make('our_mission_text')
                                            ->label('Mission Text')
                                            ->rows(4),
                                        FileUpload::make('our_mission_image')
                                            ->label('Mission Image')
                                            ->image(),
                                    ]),
                                Section::make('Values & Culture')
                                    ->schema([
                                        Textarea::make('our_values')
                                            ->label('Our Values')
                                            ->rows(5),
                                        Textarea::make('our_culture')
                                            ->label('Our Culture')
                                            ->rows(5),
                                        Textarea::make('our_promise')
                                            ->label('Our Promise')
                                            ->rows(5),
                                    ])->columns(3),
                            ]),
                    ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Page Settings')
                        ->schema([
                            Toggle::make('show_in_main_nav')
                                ->label('Show in Main Navigation'),
                            Toggle::make('show_in_footer')
                                ->label('Show in Footer'),
                            Toggle::make('allow_indexing')
                                ->label('Allow Indexing')
                                ->default(true),
                        ]),
                        
                    Section::make('SEO')
                        ->schema([
                            TextInput::make('meta_title')
                                ->label('Meta Title'),
                            Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->rows(3),
                            TextInput::make('meta_keywords')
                                ->label('Meta Keywords'),
                            TextInput::make('canonical_url')
                                ->label('Canonical URL')
                                ->url(),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    public static function getMockPages(): array
    {
        return [
            1 => ['id' => 1, 'title' => 'Home', 'page_type' => 'Standard', 'slug' => 'home', 'status' => 'Published', 'seo_enabled' => true, 'last_updated' => '2023-11-01', 'show_in_main_nav' => true],
            2 => ['id' => 2, 'title' => 'About Us', 'page_type' => 'Standard', 'slug' => 'about-us', 'status' => 'Published', 'seo_enabled' => true, 'last_updated' => '2023-10-15', 'show_in_main_nav' => true],
            3 => ['id' => 3, 'title' => 'Our Brands', 'page_type' => 'Standard', 'slug' => 'our-brands', 'status' => 'Published', 'seo_enabled' => true, 'last_updated' => '2023-10-18', 'show_in_main_nav' => true],
            4 => ['id' => 4, 'title' => 'Sustainability', 'page_type' => 'Standard', 'slug' => 'sustainability', 'status' => 'Draft', 'seo_enabled' => false, 'last_updated' => '2023-11-02', 'show_in_main_nav' => false],
            5 => ['id' => 5, 'title' => 'Careers', 'page_type' => 'Standard', 'slug' => 'careers', 'status' => 'Published', 'seo_enabled' => true, 'last_updated' => '2023-09-20', 'show_in_main_nav' => true],
            6 => ['id' => 6, 'title' => 'Membership', 'page_type' => 'Landing Page', 'slug' => 'membership', 'status' => 'Published', 'seo_enabled' => true, 'last_updated' => '2023-10-25', 'show_in_main_nav' => true],
            7 => ['id' => 7, 'title' => 'Contact Us', 'page_type' => 'Standard', 'slug' => 'contact-us', 'status' => 'Published', 'seo_enabled' => true, 'last_updated' => '2023-08-10', 'show_in_main_nav' => true],
            8 => ['id' => 8, 'title' => 'Privacy Policy', 'page_type' => 'Legal', 'slug' => 'privacy-policy', 'status' => 'Published', 'seo_enabled' => true, 'last_updated' => '2022-12-05', 'show_in_footer' => true],
            9 => ['id' => 9, 'title' => 'Terms & Conditions', 'page_type' => 'Legal', 'slug' => 'terms-and-conditions', 'status' => 'Published', 'seo_enabled' => true, 'last_updated' => '2022-12-05', 'show_in_footer' => true],
            10 => ['id' => 10, 'title' => 'Cookie Policy', 'page_type' => 'Legal', 'slug' => 'cookie-policy', 'status' => 'Draft', 'seo_enabled' => true, 'last_updated' => '2023-11-03', 'show_in_footer' => true],
        ];
    }
}
