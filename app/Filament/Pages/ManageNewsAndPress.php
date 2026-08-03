<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class ManageNewsAndPress extends Page
{
    protected string $view = 'filament.pages.manage-news-and-press';

    public $searchQuery = '';
    public $filterCategory = '';
    public $filterStatus = '';
    public $filterFeatured = '';
    public $filterPublishDate = '';


    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterCategory(): void { $this->currentPage = 1; }
    public function updatedFilterStatus(): void { $this->currentPage = 1; }
    public function updatedFilterFeatured(): void { $this->currentPage = 1; }
    public function updatedFilterPublishDate(): void { $this->currentPage = 1; }
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
        return 5;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-newspaper';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'News & Press';
    }

    public function getTitle(): string | Htmlable
    {
        return 'News & Press';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'News & Press';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage all news articles, press releases, blogs and announcements published on the HMH Hotel Group website.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addArticle')
                ->label('Add Article')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getArticleFormSchema())
                
            ->url(\App\Filament\Pages\NewsAndPress\CreateNewsAndPress::getUrl())
            ->action(function (array $data) {
                    Notification::make()
                        ->title('Article saved successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewArticleAction(): Action
    {
        return Action::make('viewArticle')
            ->modalHeading('View Article')
            ->modalWidth('7xl')
            ->form($this->getArticleFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockArticles()[$arguments['id']] ?? [])
            ->disabledForm()
            
            ->url(fn (array $arguments) => \App\Filament\Pages\NewsAndPress\ViewNewsAndPress::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editArticleAction(): Action
    {
        return Action::make('editArticle')
            ->modalHeading('Edit Article')
            ->modalWidth('7xl')
            ->form($this->getArticleFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockArticles()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\NewsAndPress\EditNewsAndPress::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Article saved successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteArticleAction(): Action
    {
        return Action::make('deleteArticle')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Article deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public static function getArticleFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('title')
                                    ->label('Article Title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $set('slug', Str::slug($state))),
                                TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required(),
                            ]),
                            Grid::make(2)->schema([
                                Select::make('category')
                                    ->label('Category')
                                    ->options([
                                        'News' => 'News',
                                        'Press Release' => 'Press Release',
                                        'Announcement' => 'Announcement',
                                        'Blog' => 'Blog',
                                        'Award' => 'Award',
                                    ])
                                    ->required(),
                                TextInput::make('author')
                                    ->label('Author')
                                    ->required(),
                            ]),
                            Grid::make(3)->schema([
                                DatePicker::make('publish_date')
                                    ->label('Publish Date'),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'Published' => 'Published',
                                        'Draft' => 'Draft',
                                    ])
                                    ->default('Published')
                                    ->required(),
                                Toggle::make('featured')
                                    ->label('Featured Article')
                                    ->default(false),
                            ]),
                        ]),

                    Section::make('Content')
                        ->schema([
                            \App\Filament\Forms\Components\JoditEditor::make('short_description')
                                ->label('Short Description'),
                            \App\Filament\Forms\Components\JoditEditor::make('content')
                                ->label('Rich Text Content'),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            FileUpload::make('featured_image')
                                ->label('Featured Image Upload')
                                ->image(),
                            FileUpload::make('gallery')
                                ->label('Gallery Images Upload')
                                ->image()
                                ->multiple(),
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

    public static function getMockArticles(): array
    {
        return [
            1 => ['id' => 1, 'title' => 'HMH Opens New Hotel in Dubai', 'category' => 'Press Release', 'slug' => 'hmh-opens-new-hotel-in-dubai', 'author' => 'Corporate Communications', 'publish_date' => '2023-11-01', 'status' => 'Published', 'featured' => true, 'last_updated' => '2023-11-01'],
            2 => ['id' => 2, 'title' => 'Summer Stay Campaign 2026', 'category' => 'Announcement', 'slug' => 'summer-stay-campaign-2026', 'author' => 'Marketing Team', 'publish_date' => '2023-10-15', 'status' => 'Published', 'featured' => false, 'last_updated' => '2023-10-15'],
            3 => ['id' => 3, 'title' => 'Hospitality Excellence Award', 'category' => 'Award', 'slug' => 'hospitality-excellence-award', 'author' => 'PR Department', 'publish_date' => '2023-10-05', 'status' => 'Published', 'featured' => true, 'last_updated' => '2023-10-05'],
            4 => ['id' => 4, 'title' => 'New Sustainability Initiative', 'category' => 'News', 'slug' => 'new-sustainability-initiative', 'author' => 'Sustainability Board', 'publish_date' => '2023-09-28', 'status' => 'Published', 'featured' => false, 'last_updated' => '2023-09-28'],
            5 => ['id' => 5, 'title' => 'New Executive Chef Announcement', 'category' => 'Announcement', 'slug' => 'new-executive-chef-announcement', 'author' => 'HR Department', 'publish_date' => '2023-09-20', 'status' => 'Draft', 'featured' => false, 'last_updated' => '2023-11-02'],
            6 => ['id' => 6, 'title' => 'Annual Business Conference', 'category' => 'Press Release', 'slug' => 'annual-business-conference', 'author' => 'Corporate Events', 'publish_date' => '2023-09-15', 'status' => 'Published', 'featured' => false, 'last_updated' => '2023-09-15'],
            7 => ['id' => 7, 'title' => 'Hotel Renovation Completed', 'category' => 'News', 'slug' => 'hotel-renovation-completed', 'author' => 'Operations Team', 'publish_date' => '2023-09-10', 'status' => 'Published', 'featured' => false, 'last_updated' => '2023-09-10'],
            8 => ['id' => 8, 'title' => 'Corporate Partnership Announcement', 'category' => 'Press Release', 'slug' => 'corporate-partnership-announcement', 'author' => 'Corporate Communications', 'publish_date' => '2023-11-05', 'status' => 'Draft', 'featured' => false, 'last_updated' => '2023-11-04'],
        ];
    }
}
