<?php
namespace App\Filament\Pages\NewsAndPress;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditNewsAndPress extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-news-and-press/{record}/edit';
    

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $article = \App\Models\NewsPost::findOrFail($record);
        $bodyData = is_array($article->body) ? ($article->body['en'] ?? '') : $article->body;
        $decodedBody = json_decode((string)$bodyData, true) ?? [];
        if (!is_array($decodedBody)) {
            $decodedBody = ['content' => $bodyData];
        }

        $this->form->fill([
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
        ]);
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageNewsAndPress::getArticleFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        \App\Models\NewsPost::findOrFail($this->record)->update([
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
        
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageNewsAndPress::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageNewsAndPress::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
