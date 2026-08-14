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

    protected function getViewData(): array
    {
        $query = \App\Models\NewsPost::query();

        if ($this->searchQuery) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('author_name', 'like', '%' . $this->searchQuery . '%');
            });
        }
        if ($this->filterCategory) {
            // Map category names back to channels
            $channel = $this->filterCategory === 'Press Release' ? 'press-release' : 'news';
            $query->where('channel', $channel);
        }
        if ($this->filterStatus !== '') {
            $isActive = $this->filterStatus === 'Published';
            $query->where('is_active', $isActive);
        }
        if ($this->filterPublishDate) {
            $query->whereDate('published_at', $this->filterPublishDate);
        }
        
        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        
        $articles = $query->skip(($currentPage - 1) * $this->perPage)
                        ->take($this->perPage)
                        ->get()
                        ->map(function ($article) {
                            return [
                                'id' => $article->id,
                                'title' => $article->title,
                                'category' => $article->channel === 'press-release' ? 'Press Release' : 'News',
                                'slug' => $article->slug,
                                'author' => $article->author_name ?? 'System',
                                'publish_date' => $article->published_at ? \Carbon\Carbon::parse($article->published_at)->format('Y-m-d') : null,
                                'status' => $article->is_active ? 'Published' : 'Draft',
                                'featured' => false,
                                'last_updated' => $article->updated_at?->format('Y-m-d') ?? '',
                            ];
                        });
                        
        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        // Calculate stats for the cards
        $totalArticles = \App\Models\NewsPost::count();
        $publishedArticles = \App\Models\NewsPost::where('is_active', 1)->count();
        $draftArticles = \App\Models\NewsPost::where('is_active', 0)->count();
        $featuredArticles = 0; // Featured is not tracked in DB yet, but we'll return 0 to be safe

        return compact(
            'totalItems', 'lastPage', 'currentPage', 'articles', 'from', 'to',
            'totalArticles', 'publishedArticles', 'draftArticles', 'featuredArticles'
        );
    }

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
                    \App\Models\NewsPost::create([
                        'title' => ['en' => $data['title'] ?? ''],
                        'slug' => $data['slug'],
                        'channel' => (strtolower(str_replace(' ', '-', $data['category'] ?? '')) === 'press-release') ? 'press-release' : 'news',
                        'published_at' => $data['publish_date'] ?? now(),
                        'is_active' => ($data['status'] ?? 'Published') === 'Published' ? 1 : 0,
                        'meta_title' => ['en' => $data['meta_title'] ?? ''],
                        'meta_description' => ['en' => $data['meta_description'] ?? ''],
                        'body' => ['en' => json_encode([
                            'content' => $data['content'] ?? '',
                            'author' => $data['author'] ?? '',
                            'featured_article' => $data['featured_article'] ?? false,
                            'featured_image' => $data['featured_image'] ?? null,
                            'gallery_images' => $data['gallery_images'] ?? [],
                            'meta_keywords' => $data['meta_keywords'] ?? '',
                            'canonical_url' => $data['canonical_url'] ?? '',
                        ])],
                    ]);
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
            ->fillForm(function (array $arguments) {
                $article = \App\Models\NewsPost::find($arguments['id']);
                if (!$article) return [];
                
                $bodyData = is_array($article->body) ? ($article->body['en'] ?? '') : $article->body;
                $decodedBody = json_decode((string)$bodyData, true) ?? [];
                if (!is_array($decodedBody)) {
                    $decodedBody = ['content' => $bodyData];
                }

                return [
                    'title' => is_array($article->title) ? ($article->title['en'] ?? '') : $article->title,
                    'slug' => $article->slug,
                    'category' => $article->channel === 'press-release' ? 'Press Release' : 'News',
                    'publish_date' => $article->published_at,
                    'status' => $article->is_active ? 'Published' : 'Draft',
                    'meta_title' => is_array($article->meta_title) ? ($article->meta_title['en'] ?? '') : $article->meta_title,
                    'meta_description' => is_array($article->meta_description) ? ($article->meta_description['en'] ?? '') : $article->meta_description,
                    'content' => $decodedBody['content'] ?? '',
                    'author' => $decodedBody['author'] ?? '',
                    'featured_article' => $decodedBody['featured_article'] ?? false,
                    'featured_image' => $decodedBody['featured_image'] ?? null,
                    'gallery_images' => $decodedBody['gallery_images'] ?? [],
                    'meta_keywords' => $decodedBody['meta_keywords'] ?? '',
                    'canonical_url' => $decodedBody['canonical_url'] ?? '',
                ];
            })
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
            ->fillForm(function (array $arguments) {
                $article = \App\Models\NewsPost::find($arguments['id']);
                if (!$article) return [];
                
                $bodyData = is_array($article->body) ? ($article->body['en'] ?? '') : $article->body;
                $decodedBody = json_decode((string)$bodyData, true) ?? [];
                if (!is_array($decodedBody)) {
                    $decodedBody = ['content' => $bodyData];
                }

                return [
                    'title' => is_array($article->title) ? ($article->title['en'] ?? '') : $article->title,
                    'slug' => $article->slug,
                    'category' => $article->channel === 'press-release' ? 'Press Release' : 'News',
                    'publish_date' => $article->published_at,
                    'status' => $article->is_active ? 'Published' : 'Draft',
                    'meta_title' => is_array($article->meta_title) ? ($article->meta_title['en'] ?? '') : $article->meta_title,
                    'meta_description' => is_array($article->meta_description) ? ($article->meta_description['en'] ?? '') : $article->meta_description,
                    'content' => $decodedBody['content'] ?? '',
                    'author' => $decodedBody['author'] ?? '',
                    'featured_article' => $decodedBody['featured_article'] ?? false,
                    'featured_image' => $decodedBody['featured_image'] ?? null,
                    'gallery_images' => $decodedBody['gallery_images'] ?? [],
                    'meta_keywords' => $decodedBody['meta_keywords'] ?? '',
                    'canonical_url' => $decodedBody['canonical_url'] ?? '',
                ];
            })
            
            ->url(fn (array $arguments) => \App\Filament\Pages\NewsAndPress\EditNewsAndPress::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data, array $arguments) {
                \App\Models\NewsPost::find($arguments['id'])?->update([
                    'title' => ['en' => $data['title'] ?? ''],
                    'slug' => $data['slug'],
                    'channel' => (strtolower(str_replace(' ', '-', $data['category'] ?? '')) === 'press-release') ? 'press-release' : 'news',
                    'published_at' => $data['publish_date'] ?? now(),
                    'is_active' => ($data['status'] ?? 'Published') === 'Published' ? 1 : 0,
                    'meta_title' => ['en' => $data['meta_title'] ?? ''],
                    'meta_description' => ['en' => $data['meta_description'] ?? ''],
                    'body' => ['en' => json_encode([
                        'content' => $data['content'] ?? '',
                        'author' => $data['author'] ?? '',
                        'featured_article' => $data['featured_article'] ?? false,
                        'featured_image' => $data['featured_image'] ?? null,
                        'gallery_images' => $data['gallery_images'] ?? [],
                        'meta_keywords' => $data['meta_keywords'] ?? '',
                        'canonical_url' => $data['canonical_url'] ?? '',
                    ])],
                ]);
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
            ->action(function (array $arguments) {
                \App\Models\NewsPost::find($arguments['id'])?->delete();
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
                                    ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug($state))),
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

    // Mock Data removed
}
