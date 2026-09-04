<?php
namespace App\Filament\Pages\CmsPages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditCmsPage extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-cms-pages/{record}/edit';
    

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
        return $form->schema(\App\Filament\Pages\ManageCmsPages::getPageFormSchema())
            ->statePath('data')
            ->model(\App\Models\Page::class);
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $page = \App\Models\Page::find($this->record);
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

            $existingBody = is_array($page->body) ? ($page->body['en'] ?? '') : $page->body;
            $decodedBody = json_decode((string)$existingBody, true) ?? [];

            $bannerImages = $data['banner_images'] ?? ($decodedBody['banner_images'] ?? []);
            $bannerSlides = $data['banner_slides'] ?? ($decodedBody['banner_slides'] ?? []);

            if (!empty($bannerImages)) {
                $existingSlideImages = array_column($bannerSlides, 'image');
                foreach ($bannerImages as $bImg) {
                    if (!in_array($bImg, $existingSlideImages)) {
                        $bannerSlides[] = [
                            'image' => $bImg,
                            'title' => is_array($data['title'] ?? null) ? ($data['title']['en'] ?? '') : ($data['title'] ?? ''),
                            'subtitle' => $data['intro_subtitle'] ?? 'Hospitality Management Holding',
                        ];
                    }
                }
            }

            $page->update([
                'title' => ['en' => $data['title'] ?? ''],
                'page_type' => $pageType,
                'slug' => $slug,
                'is_active' => ($data['status'] ?? 'Published') === 'Published',
                'meta_title' => ['en' => $data['meta_title'] ?? ''],
                'meta_description' => ['en' => $data['meta_description'] ?? ''],
                'body' => ['en' => json_encode([
                    'display_order' => $data['display_order'] ?? null,
                    'banner_images' => $data['banner_images'] ?? ($decodedBody['banner_images'] ?? []),
                    'banner_slides' => $bannerSlides,
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
                    'categories' => $data['categories'] ?? ($decodedBody['categories'] ?? []),
                    'corp_amman_images' => $data['corp_amman_images'] ?? ($decodedBody['corp_amman_images'] ?? []),
                    'coral_beach_sharjah_images' => $data['coral_beach_sharjah_images'] ?? ($decodedBody['coral_beach_sharjah_images'] ?? []),
                    'bahi_ajman_palace_images' => $data['bahi_ajman_palace_images'] ?? ($decodedBody['bahi_ajman_palace_images'] ?? []),
                    'ecos_dubai_images' => $data['ecos_dubai_images'] ?? ($decodedBody['ecos_dubai_images'] ?? []),
                    'coral_dubai_deira_images' => $data['coral_dubai_deira_images'] ?? ($decodedBody['coral_dubai_deira_images'] ?? []),
                    'coral_jubail_images' => $data['coral_jubail_images'] ?? ($decodedBody['coral_jubail_images'] ?? []),
                    'gallery_items' => $data['gallery_items'] ?? ($decodedBody['gallery_items'] ?? []),
                    'future_slider_images' => $data['future_slider_images'] ?? ($decodedBody['future_slider_images'] ?? []),
                    'value_proposition_title' => $data['value_proposition_title'] ?? ($decodedBody['value_proposition_title'] ?? ''),
                    'value_proposition_text' => $data['value_proposition_text'] ?? ($decodedBody['value_proposition_text'] ?? ''),
                    'brands_list' => $data['brands_list'] ?? ($decodedBody['brands_list'] ?? []),
                    'services_title' => $data['services_title'] ?? ($decodedBody['services_title'] ?? ''),
                    'services_intro' => $data['services_intro'] ?? ($decodedBody['services_intro'] ?? ''),
                    'services_list' => $data['services_list'] ?? ($decodedBody['services_list'] ?? []),
                    'terms_accordion' => $data['terms_accordion'] ?? ($decodedBody['terms_accordion'] ?? []),
                    'privacy_accordion' => $data['privacy_accordion'] ?? ($decodedBody['privacy_accordion'] ?? []),
                    'privacy_slider_images' => $data['privacy_slider_images'] ?? ($decodedBody['privacy_slider_images'] ?? []),
                    'terms_slider_images' => $data['terms_slider_images'] ?? ($decodedBody['terms_slider_images'] ?? []),
                    'press_releases_list' => $data['press_releases_list'] ?? ($decodedBody['press_releases_list'] ?? []),
                    'meta_keywords' => $data['meta_keywords'] ?? '',
                    'canonical_url' => $data['canonical_url'] ?? '',
                ])],
            ]);
        }
                    \Illuminate\Support\Facades\Cache::flush();

            $nextUploads = 'C:/Projects/operahotels/operahotels/public/uploads';
            if (is_dir($nextUploads)) {
                @shell_exec('xcopy /Y /D /E "C:\\xampp\\htdocs\\hmh_hotel\\hmh_hotel\\public\\uploads\\*" "' . $nextUploads . '\\"');
            }

            \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageCmsPages::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageCmsPages::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
