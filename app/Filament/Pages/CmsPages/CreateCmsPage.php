<?php
namespace App\Filament\Pages\CmsPages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateCmsPage extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-cms-pages/create';
    

    public ?array $data = [];

    public function mount(): void
    {
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageCmsPages::getPageFormSchema())
            ->statePath('data')
            ->model(\App\Models\Page::class);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
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
            'is_active' => ($data['status'] ?? 'Published') === 'Published',
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
                'partners_list' => $data['partners_list'] ?? [],
                'team_members_list' => $data['team_members_list'] ?? [],
                'history_timeline' => $data['history_timeline'] ?? [],
                'coming_soon_sections' => $data['coming_soon_sections'] ?? [],
                'categories' => $data['categories'] ?? [],
                'press_releases_list' => $data['press_releases_list'] ?? [],
                'terms_accordion' => $data['terms_accordion'] ?? [],
                'privacy_accordion' => $data['privacy_accordion'] ?? [],
                'privacy_slider_images' => $data['privacy_slider_images'] ?? [],
                'terms_slider_images' => $data['terms_slider_images'] ?? [],
                'meta_keywords' => $data['meta_keywords'] ?? '',
                'canonical_url' => $data['canonical_url'] ?? '',
            ])],
        ]);

        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageCmsPages::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageCmsPages::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
