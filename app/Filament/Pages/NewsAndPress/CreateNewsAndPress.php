<?php
namespace App\Filament\Pages\NewsAndPress;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateNewsAndPress extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-news-and-press/create';
    

    public ?array $data = [];

    public function mount(): void
    {
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageNewsAndPress::getArticleFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
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
        
        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageNewsAndPress::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageNewsAndPress::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
