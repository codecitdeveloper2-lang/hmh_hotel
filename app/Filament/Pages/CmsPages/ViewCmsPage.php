<?php
namespace App\Filament\Pages\CmsPages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewCmsPage extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-cms-pages/{record}/view';
    

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $page = \App\Models\Page::find($this->record);
        $data = [];
        if ($page) {
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

            $data = [
                'title' => is_array($page->title) ? ($page->title['en'] ?? '') : $page->title,
                'page_type' => $formPageType,
                'slug' => $page->slug,

                'status' => $page->is_active ? 'Published' : 'Draft',
                'meta_title' => is_array($page->meta_title) ? ($page->meta_title['en'] ?? '') : $page->meta_title,
                'meta_description' => is_array($page->meta_description) ? ($page->meta_description['en'] ?? '') : $page->meta_description,
                'display_order' => $decodedBody['display_order'] ?? null,
                'banner_images' => $decodedBody['banner_images'] ?? [],
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
                'partners_list' => $decodedBody['partners_list'] ?? [],
                'team_members_list' => $decodedBody['team_members_list'] ?? [],
                'history_timeline' => $decodedBody['history_timeline'] ?? [],
                'coming_soon_sections' => $decodedBody['coming_soon_sections'] ?? [],
                'categories' => $decodedBody['categories'] ?? [],
                'corp_amman_images' => $decodedBody['corp_amman_images'] ?? [],
                'coral_beach_sharjah_images' => $decodedBody['coral_beach_sharjah_images'] ?? [],
                'bahi_ajman_palace_images' => $decodedBody['bahi_ajman_palace_images'] ?? [],
                'ecos_dubai_images' => $decodedBody['ecos_dubai_images'] ?? [],
                'coral_dubai_deira_images' => $decodedBody['coral_dubai_deira_images'] ?? [],
                'coral_jubail_images' => $decodedBody['coral_jubail_images'] ?? [],
                'gallery_items' => $decodedBody['gallery_items'] ?? [],
                'future_slider_images' => $decodedBody['future_slider_images'] ?? [],
                'value_proposition_title' => $decodedBody['value_proposition_title'] ?? '',
                'value_proposition_text' => $decodedBody['value_proposition_text'] ?? '',
                'brands_list' => $decodedBody['brands_list'] ?? [],
                'services_title' => $decodedBody['services_title'] ?? '',
                'services_intro' => $decodedBody['services_intro'] ?? '',
                'services_list' => $decodedBody['services_list'] ?? [],
                'terms_accordion' => $decodedBody['terms_accordion'] ?? [],
                'privacy_accordion' => $decodedBody['privacy_accordion'] ?? [],
                'privacy_slider_images' => $decodedBody['privacy_slider_images'] ?? [],
                'terms_slider_images' => $decodedBody['terms_slider_images'] ?? [],
                'press_releases_list' => $decodedBody['press_releases_list'] ?? [],
                'meta_keywords' => $decodedBody['meta_keywords'] ?? '',
                'canonical_url' => $decodedBody['canonical_url'] ?? '',
            ];
        }
        $this->form->fill($data);
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageCmsPages::getPageFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageCmsPages::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
