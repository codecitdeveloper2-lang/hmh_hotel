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

    public function gotoPage(int $page): void
    {
        $this->currentPage = $page;
    }

    protected function getViewData(): array
    {
        $query = \App\Models\Page::query();
        
        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        
        $pages = $query->skip(($currentPage - 1) * $this->perPage)
                        ->take($this->perPage)
                        ->get()
                        ->map(function ($page) {
                            return [
                                'id' => $page->id,
                                'title' => is_array($page->title) ? ($page->title['en'] ?? '') : $page->title,
                                'page_type' => ucfirst(str_replace('-', ' ', $page->page_type)),
                                'slug' => $page->slug,
                                'status' => $page->is_active ? 'Published' : 'Draft',
                                'seo_enabled' => !empty($page->meta_title),
                                'last_updated' => $page->updated_at?->format('Y-m-d') ?? '',
                                'show_in_main_nav' => true,
                            ];
                        });
                        
        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'pages', 'from', 'to');
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
                    $enumTypes = ['about','careers','best-rate-guarantee','sustainability','accessibility','terms-conditions','privacy-statement','newsletter','custom'];
                    $pageType = 'custom';
                    $slug = $data['slug'] ?? \Illuminate\Support\Str::slug($data['title'] ?? 'new-page');
                    foreach ($enumTypes as $type) {
                        if (strpos($slug, $type) !== false || $slug === $type || strpos($slug, str_replace('-', '', $type)) !== false) {
                            $pageType = $type;
                            break;
                        }
                    }
                    if ($slug === 'privacy-policy') $pageType = 'privacy-statement';
                    if ($slug === 'terms-and-conditions') $pageType = 'terms-conditions';
                    if ($slug === 'about-us') $pageType = 'about';

                    \App\Models\Page::create([
                        'title' => ['en' => $data['title'] ?? ''],
                        'page_type' => $pageType,
                        'slug' => $slug,
                        'is_active' => ($data['status'] ?? 'Published') === 'Published' ? 1 : 0,
                        'meta_title' => ['en' => $data['meta_title'] ?? ''],
                        'meta_description' => ['en' => $data['meta_description'] ?? ''],
                        'body' => ['en' => json_encode([

                            'display_order' => $data['display_order'] ?? null,
                            'banner_slides' => $data['banner_slides'] ?? [],
                            'content_title' => $data['content_title'] ?? '',
                            'content' => $data['content'] ?? '',
                            'cta_text' => $data['cta_text'] ?? '',
                            'cta_link' => $data['cta_link'] ?? '',
                            'intro_subtitle' => $data['intro_subtitle'] ?? '',
                            'intro_title' => $data['intro_title'] ?? '',
                            'intro_text' => $data['intro_text'] ?? '',
                            'expansion_image' => $data['expansion_image'] ?? '',
                            'expansion_text' => $data['expansion_text'] ?? '',
                            'our_vision_text' => $data['our_vision_text'] ?? '',
                            'our_vision_image' => $data['our_vision_image'] ?? '',
                            'our_mission_text' => $data['our_mission_text'] ?? '',
                            'our_mission_image' => $data['our_mission_image'] ?? '',
                            'our_values' => $data['our_values'] ?? '',
                            'our_culture' => $data['our_culture'] ?? '',
                            'our_promise' => $data['our_promise'] ?? '',
                            'responsibilities_list' => $data['responsibilities_list'] ?? [],
                            'coming_soon_sections' => $data['coming_soon_sections'] ?? [],
                            'meta_keywords' => $data['meta_keywords'] ?? '',
                            'canonical_url' => $data['canonical_url'] ?? '',
                        ])],
                    ]);
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
            ->fillForm(function (array $arguments) {
                $page = \App\Models\Page::find($arguments['id'] ?? null);
                if (!$page) return [];
                
                $dbPageType = $page->page_type;
                $formPageType = 'Standard';
                if (in_array($dbPageType, ['privacy-statement', 'terms-conditions'])) {
                    $formPageType = 'Legal';
                } elseif ($dbPageType === 'newsletter') {
                    $formPageType = 'Landing Page';
                }

                $bodyData = is_array($page->body) ? ($page->body['en'] ?? '') : $page->body;
                $decodedBody = json_decode((string)$bodyData, true) ?? [];
                if (!is_array($decodedBody)) {
                    $decodedBody = ['content' => $bodyData];
                }

                return [
                    'title' => is_array($page->title) ? ($page->title['en'] ?? '') : $page->title,
                    'page_type' => $formPageType,
                    'slug' => $page->slug,
                    'status' => $page->is_active ? 'Published' : 'Draft',
                    'meta_title' => is_array($page->meta_title) ? ($page->meta_title['en'] ?? '') : $page->meta_title,
                    'meta_description' => is_array($page->meta_description) ? ($page->meta_description['en'] ?? '') : $page->meta_description,

                    'display_order' => $decodedBody['display_order'] ?? null,
                    'banner_slides' => $decodedBody['banner_slides'] ?? [],
                    'content_title' => $decodedBody['content_title'] ?? '',
                    'content' => $decodedBody['content'] ?? '',
                    'cta_text' => $decodedBody['cta_text'] ?? '',
                    'cta_link' => $decodedBody['cta_link'] ?? '',
                    'intro_subtitle' => $decodedBody['intro_subtitle'] ?? '',
                    'intro_title' => $decodedBody['intro_title'] ?? '',
                    'intro_text' => $decodedBody['intro_text'] ?? '',
                    'expansion_image' => $decodedBody['expansion_image'] ?? '',
                    'expansion_text' => $decodedBody['expansion_text'] ?? '',
                    'our_vision_text' => $decodedBody['our_vision_text'] ?? '',
                    'our_vision_image' => $decodedBody['our_vision_image'] ?? '',
                    'our_mission_text' => $decodedBody['our_mission_text'] ?? '',
                    'our_mission_image' => $decodedBody['our_mission_image'] ?? '',
                    'our_values' => $decodedBody['our_values'] ?? '',
                    'our_culture' => $decodedBody['our_culture'] ?? '',
                    'our_promise' => $decodedBody['our_promise'] ?? '',
                    'responsibilities_list' => $decodedBody['responsibilities_list'] ?? [],
                    'coming_soon_sections' => $decodedBody['coming_soon_sections'] ?? [],
                    'meta_keywords' => $decodedBody['meta_keywords'] ?? '',
                    'canonical_url' => $decodedBody['canonical_url'] ?? '',
                ];
            })
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
            ->fillForm(function (array $arguments) {
                $page = \App\Models\Page::find($arguments['id']);
                if (!$page) return [];
                
                $dbPageType = $page->page_type;
                $formPageType = 'Standard';
                if (in_array($dbPageType, ['privacy-statement', 'terms-conditions'])) {
                    $formPageType = 'Legal';
                } elseif ($dbPageType === 'newsletter') {
                    $formPageType = 'Landing Page';
                }

                $bodyData = is_array($page->body) ? ($page->body['en'] ?? '') : $page->body;
                $decodedBody = json_decode((string)$bodyData, true) ?? [];
                if (!is_array($decodedBody)) {
                    $decodedBody = ['content' => $bodyData];
                }

                return [
                    'title' => is_array($page->title) ? ($page->title['en'] ?? '') : $page->title,
                    'page_type' => $formPageType,
                    'slug' => $page->slug,
                    'status' => $page->is_active ? 'Published' : 'Draft',
                    'meta_title' => is_array($page->meta_title) ? ($page->meta_title['en'] ?? '') : $page->meta_title,
                    'meta_description' => is_array($page->meta_description) ? ($page->meta_description['en'] ?? '') : $page->meta_description,

                    'display_order' => $decodedBody['display_order'] ?? null,
                    'banner_slides' => $decodedBody['banner_slides'] ?? [],
                    'content_title' => $decodedBody['content_title'] ?? '',
                    'content' => $decodedBody['content'] ?? '',
                    'cta_text' => $decodedBody['cta_text'] ?? '',
                    'cta_link' => $decodedBody['cta_link'] ?? '',
                    'intro_subtitle' => $decodedBody['intro_subtitle'] ?? '',
                    'intro_title' => $decodedBody['intro_title'] ?? '',
                    'intro_text' => $decodedBody['intro_text'] ?? '',
                    'expansion_image' => $decodedBody['expansion_image'] ?? '',
                    'expansion_text' => $decodedBody['expansion_text'] ?? '',
                    'our_vision_text' => $decodedBody['our_vision_text'] ?? '',
                    'our_vision_image' => $decodedBody['our_vision_image'] ?? '',
                    'our_mission_text' => $decodedBody['our_mission_text'] ?? '',
                    'our_mission_image' => $decodedBody['our_mission_image'] ?? '',
                    'our_values' => $decodedBody['our_values'] ?? '',
                    'our_culture' => $decodedBody['our_culture'] ?? '',
                    'our_promise' => $decodedBody['our_promise'] ?? '',
                    'responsibilities_list' => $decodedBody['responsibilities_list'] ?? [],
                    'coming_soon_sections' => $decodedBody['coming_soon_sections'] ?? [],
                    'meta_keywords' => $decodedBody['meta_keywords'] ?? '',
                    'canonical_url' => $decodedBody['canonical_url'] ?? '',
                ];
            })
            
            ->url(fn (array $arguments) => \App\Filament\Pages\CmsPages\EditCmsPage::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data, array $arguments) {
                $page = \App\Models\Page::find($arguments['id']);
                if ($page) {
                    $enumTypes = ['about','careers','best-rate-guarantee','sustainability','accessibility','terms-conditions','privacy-statement','newsletter','custom'];
                    $pageType = 'custom';
                    $slug = $data['slug'] ?? '';
                    foreach ($enumTypes as $type) {
                        if (strpos($slug, $type) !== false || $slug === $type || strpos($slug, str_replace('-', '', $type)) !== false) {
                            $pageType = $type;
                            break;
                        }
                    }
                    if ($slug === 'privacy-policy') $pageType = 'privacy-statement';
                    if ($slug === 'terms-and-conditions') $pageType = 'terms-conditions';
                    if ($slug === 'about-us') $pageType = 'about';

                    $page->update([
                        'title' => ['en' => $data['title'] ?? ''],
                        'page_type' => $pageType,
                        'slug' => $slug,
                        'is_active' => ($data['status'] ?? 'Published') === 'Published' ? 1 : 0,
                        'meta_title' => ['en' => $data['meta_title'] ?? ''],
                        'meta_description' => ['en' => $data['meta_description'] ?? ''],
                        'body' => ['en' => json_encode([
                            'content' => $data['content'] ?? '',
                            'cta_text' => $data['cta_text'] ?? '',
                            'cta_link' => $data['cta_link'] ?? '',
                            'intro_subtitle' => $data['intro_subtitle'] ?? '',
                            'intro_title' => $data['intro_title'] ?? '',
                            'intro_text' => $data['intro_text'] ?? '',
                            'expansion_image' => $data['expansion_image'] ?? '',
                            'expansion_text' => $data['expansion_text'] ?? '',
                            'our_vision_text' => $data['our_vision_text'] ?? '',
                            'our_vision_image' => $data['our_vision_image'] ?? '',
                            'our_mission_text' => $data['our_mission_text'] ?? '',
                            'our_mission_image' => $data['our_mission_image'] ?? '',
                            'our_values' => $data['our_values'] ?? '',
                            'our_culture' => $data['our_culture'] ?? '',
                            'our_promise' => $data['our_promise'] ?? '',
                            'responsibilities_list' => $data['responsibilities_list'] ?? [],
                            'coming_soon_sections' => $data['coming_soon_sections'] ?? [],
                            'meta_keywords' => $data['meta_keywords'] ?? '',
                            'canonical_url' => $data['canonical_url'] ?? '',
                        ])],
                    ]);
                }
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
            ->action(function (array $arguments) {
                \App\Models\Page::find($arguments['id'])?->delete();
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
                                        ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug($state))),
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
                                            ->image()->disk('uploads'),
                                        TextInput::make('subtitle')
                                            ->label('Subtitle')
                                            ->placeholder('e.g. Welcome To'),
                                        TextInput::make('title')
                                            ->label('Title'),
                                    ])
                                    ->defaultItems(1)
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Slide'),
                            ]),

                        Section::make('Page Intro Description')
                            ->schema([
                                TextInput::make('intro_subtitle')
                                    ->label('Subtitle')
                                    ->placeholder('e.g. YOU ARE UNIQUE FOR US'),
                                TextInput::make('intro_title')
                                    ->label('Title')
                                    ->placeholder('e.g. WELCOME TO OPERA GRAND HOTEL'),
                                \App\Filament\Forms\Components\JoditEditor::make('content')
                                    ->label('Description'),
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
                                            ->image()->disk('uploads'),
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
                                            ->image()->disk('uploads'),
                                    ]),
                                Section::make('Our Mission')
                                    ->schema([
                                        Textarea::make('our_mission_text')
                                            ->label('Mission Text')
                                            ->rows(4),
                                        FileUpload::make('our_mission_image')
                                            ->label('Mission Image')
                                            ->image()->disk('uploads'),
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

                        \Filament\Schemas\Components\Group::make()
                            ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => $get('slug') === 'corporate-responsibility')
                            ->schema([
                                Section::make('Responsibilities')
                                    ->schema([
                                        \Filament\Forms\Components\Repeater::make('responsibilities_list')
                                            ->label('Responsibilities')
                                            ->schema([
                                                TextInput::make('title')
                                                    ->label('Title')
                                                    ->required(),
                                                FileUpload::make('image')
                                                    ->label('Image')
                                                    ->image()->disk('uploads'),
                                                \App\Filament\Forms\Components\JoditEditor::make('description')
                                                    ->label('Description'),
                                            ])
                                            ->defaultItems(1)
                                            ->collapsible()
                                            ->cloneable()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Responsibility'),
                                    ]),
                            ]),
                            
                        Section::make('Coming Soon Section')
                            ->schema([
                                \Filament\Forms\Components\Repeater::make('coming_soon_sections')
                                    ->label('Coming Soon Items')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Section Title')
                                            ->default('Coming Soon'),
                                        TextInput::make('hotel_name')
                                            ->label('Hotel Name')
                                            ->required(),
                                        Textarea::make('description')
                                            ->label('Description')
                                            ->rows(3),
                                        FileUpload::make('image')
                                            ->label('Image')
                                            ->image()->disk('uploads'),
                                    ])
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->cloneable()
                                    ->itemLabel(fn (array $state): ?string => $state['hotel_name'] ?? 'Coming Soon Item'),
                            ]),
                    ])->columnSpan(2),
                
                Grid::make(1)->schema([

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

    // Mock Data removed
}


