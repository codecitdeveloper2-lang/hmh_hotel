<?php
namespace App\Filament\Pages\NewsAndPress;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewNewsAndPress extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-news-and-press/{record}/view';
    

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
        return $form->schema(\App\Filament\Pages\ManageNewsAndPress::getArticleFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageNewsAndPress::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
