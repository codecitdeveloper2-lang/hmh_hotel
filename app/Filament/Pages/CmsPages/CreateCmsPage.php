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
        $this->form->fill([
            'activeLocale' => 'en',
            'status' => 'Published',
            'page_type' => 'Standard',
            'title' => ['en' => '', 'ar' => ''],
        ]);
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
        
        $titleEn = is_array($data['title'] ?? null) ? ($data['title']['en'] ?? '') : ($data['title'] ?? '');
        $titleAr = is_array($data['title'] ?? null) ? ($data['title']['ar'] ?? '') : '';

        $slug = $data['slug'] ?? \Illuminate\Support\Str::slug($titleEn ?: 'new-page');
        foreach ($enumTypes as $type) {
            if (strpos($slug, $type) !== false || $slug === $type || strpos($slug, str_replace('-', '', $type)) !== false) {
                $pageType = $type;
                break;
            }
        }
        if ($slug === 'privacy-policy') $pageType = 'privacy-statement';
        if ($slug === 'terms-and-conditions') $pageType = 'terms-conditions';
        if ($slug === 'about-us') $pageType = 'about';

        $metaTitleEn = is_array($data['meta_title'] ?? null) ? ($data['meta_title']['en'] ?? '') : ($data['meta_title'] ?? '');
        $metaTitleAr = is_array($data['meta_title'] ?? null) ? ($data['meta_title']['ar'] ?? '') : '';

        $metaDescEn = is_array($data['meta_description'] ?? null) ? ($data['meta_description']['en'] ?? '') : ($data['meta_description'] ?? '');
        $metaDescAr = is_array($data['meta_description'] ?? null) ? ($data['meta_description']['ar'] ?? '') : '';

        $getText = function ($field, $locale) use ($data) {
            if (is_array($data[$field] ?? null)) {
                $val = $data[$field][$locale] ?? '';
                if (empty($val) && $locale === 'ar') {
                    return $data[$field]['en'] ?? '';
                }
                return $val;
            }
            return $locale === 'en' ? ($data[$field] ?? '') : '';
        };

        // Banner slides
        $enBannerSlides = [];
        $arBannerSlides = [];
        foreach ($data['banner_slides'] ?? [] as $slide) {
            $img = $slide['image'] ?? null;
            $sTitleEn = is_array($slide['title'] ?? null) ? ($slide['title']['en'] ?? '') : ($slide['title'] ?? '');
            $sTitleAr = is_array($slide['title'] ?? null) ? ($slide['title']['ar'] ?? '') : '';
            $sSubEn = is_array($slide['subtitle'] ?? null) ? ($slide['subtitle']['en'] ?? '') : ($slide['subtitle'] ?? '');
            $sSubAr = is_array($slide['subtitle'] ?? null) ? ($slide['subtitle']['ar'] ?? '') : '';

            $enBannerSlides[] = [
                'image' => $img,
                'title' => $sTitleEn,
                'subtitle' => $sSubEn,
            ];
            $arBannerSlides[] = [
                'image' => $img,
                'title' => !empty($sTitleAr) ? $sTitleAr : $sTitleEn,
                'subtitle' => !empty($sSubAr) ? $sSubAr : $sSubEn,
            ];
        }

        // Coming soon sections
        $enComingSoon = [];
        $arComingSoon = [];
        foreach ($data['coming_soon_sections'] ?? [] as $cs) {
            $img = $cs['image'] ?? null;
            $csTitleEn = is_array($cs['title'] ?? null) ? ($cs['title']['en'] ?? '') : ($cs['title'] ?? '');
            $csTitleAr = is_array($cs['title'] ?? null) ? ($cs['title']['ar'] ?? '') : '';
            $csHotelEn = is_array($cs['hotel_name'] ?? null) ? ($cs['hotel_name']['en'] ?? '') : ($cs['hotel_name'] ?? '');
            $csHotelAr = is_array($cs['hotel_name'] ?? null) ? ($cs['hotel_name']['ar'] ?? '') : '';
            $csDescEn = is_array($cs['description'] ?? null) ? ($cs['description']['en'] ?? '') : ($cs['description'] ?? '');
            $csDescAr = is_array($cs['description'] ?? null) ? ($cs['description']['ar'] ?? '') : '';

            $enComingSoon[] = [
                'title' => $csTitleEn,
                'hotel_name' => $csHotelEn,
                'description' => $csDescEn,
                'image' => $img,
            ];
            $arComingSoon[] = [
                'title' => !empty($csTitleAr) ? $csTitleAr : $csTitleEn,
                'hotel_name' => !empty($csHotelAr) ? $csHotelAr : $csHotelEn,
                'description' => !empty($csDescAr) ? $csDescAr : $csDescEn,
                'image' => $img,
            ];
        }

        $enBody = [
            'display_order' => $data['display_order'] ?? null,
            'banner_images' => $data['banner_images'] ?? [],
            'banner_slides' => $enBannerSlides,
            'content_title' => $getText('content_title', 'en'),
            'content' => $getText('content', 'en'),
            'cta_text' => $getText('cta_text', 'en'),
            'cta_link' => $data['cta_link'] ?? '',
            'intro_subtitle' => $getText('intro_subtitle', 'en'),
            'intro_title' => $getText('intro_title', 'en'),
            'intro_text' => $getText('intro_text', 'en'),
            'expansion_image' => $data['expansion_image'] ?? '',
            'expansion_text' => $getText('expansion_text', 'en'),
            'our_vision_text' => $getText('our_vision_text', 'en'),
            'our_vision_image' => $data['our_vision_image'] ?? '',
            'our_mission_text' => $getText('our_mission_text', 'en'),
            'our_mission_image' => $data['our_mission_image'] ?? '',
            'our_values' => $getText('our_values', 'en'),
            'our_culture' => $getText('our_culture', 'en'),
            'our_promise' => $getText('our_promise', 'en'),
            'responsibilities_list' => $data['responsibilities_list'] ?? [],
            'partners_list' => $data['partners_list'] ?? [],
            'team_members_list' => $data['team_members_list'] ?? [],
            'history_timeline' => $data['history_timeline'] ?? [],
            'coming_soon_sections' => $enComingSoon,
            'categories' => $data['categories'] ?? [],
            'press_releases_list' => $data['press_releases_list'] ?? [],
            'careers_list' => $data['careers_list'] ?? [],
            'locations_list' => $data['locations_list'] ?? [],
            'hotel_contacts' => $data['hotel_contacts'] ?? [],
            'central_phone' => $data['central_phone'] ?? '',
            'central_whatsapp' => $data['central_whatsapp'] ?? '',
            'central_email' => $data['central_email'] ?? '',
            'terms_accordion' => $data['terms_accordion'] ?? [],
            'privacy_accordion' => $data['privacy_accordion'] ?? [],
            'privacy_slider_images' => $data['privacy_slider_images'] ?? [],
            'terms_slider_images' => $data['terms_slider_images'] ?? [],
            'meta_keywords' => $getText('meta_keywords', 'en'),
            'canonical_url' => $data['canonical_url'] ?? '',
        ];

        $arBody = [
            'display_order' => $data['display_order'] ?? null,
            'banner_images' => $data['banner_images'] ?? [],
            'banner_slides' => $arBannerSlides,
            'content_title' => $getText('content_title', 'ar'),
            'content' => $getText('content', 'ar'),
            'cta_text' => $getText('cta_text', 'ar'),
            'cta_link' => $data['cta_link'] ?? '',
            'intro_subtitle' => $getText('intro_subtitle', 'ar'),
            'intro_title' => $getText('intro_title', 'ar'),
            'intro_text' => $getText('intro_text', 'ar'),
            'expansion_image' => $data['expansion_image'] ?? '',
            'expansion_text' => $getText('expansion_text', 'ar'),
            'our_vision_text' => $getText('our_vision_text', 'ar'),
            'our_vision_image' => $data['our_vision_image'] ?? '',
            'our_mission_text' => $getText('our_mission_text', 'ar'),
            'our_mission_image' => $data['our_mission_image'] ?? '',
            'our_values' => $getText('our_values', 'ar'),
            'our_culture' => $getText('our_culture', 'ar'),
            'our_promise' => $getText('our_promise', 'ar'),
            'responsibilities_list' => $data['responsibilities_list'] ?? [],
            'partners_list' => $data['partners_list'] ?? [],
            'team_members_list' => $data['team_members_list'] ?? [],
            'history_timeline' => $data['history_timeline'] ?? [],
            'coming_soon_sections' => $arComingSoon,
            'categories' => $data['categories'] ?? [],
            'press_releases_list' => $data['press_releases_list'] ?? [],
            'careers_list' => $data['careers_list'] ?? [],
            'locations_list' => $data['locations_list'] ?? [],
            'hotel_contacts' => $data['hotel_contacts'] ?? [],
            'central_phone' => $data['central_phone'] ?? '',
            'central_whatsapp' => $data['central_whatsapp'] ?? '',
            'central_email' => $data['central_email'] ?? '',
            'terms_accordion' => $data['terms_accordion'] ?? [],
            'privacy_accordion' => $data['privacy_accordion'] ?? [],
            'privacy_slider_images' => $data['privacy_slider_images'] ?? [],
            'terms_slider_images' => $data['terms_slider_images'] ?? [],
            'meta_keywords' => $getText('meta_keywords', 'ar'),
            'canonical_url' => $data['canonical_url'] ?? '',
        ];

        \App\Models\Page::create([
            'title' => [
                'en' => $titleEn,
                'ar' => $titleAr,
            ],
            'page_type' => $pageType,
            'slug' => $slug,
            'is_active' => ($data['status'] ?? 'Published') === 'Published',
            'meta_title' => [
                'en' => $metaTitleEn,
                'ar' => $metaTitleAr,
            ],
            'meta_description' => [
                'en' => $metaDescEn,
                'ar' => $metaDescAr,
            ],
            'body' => [
                'en' => json_encode($enBody),
                'ar' => json_encode($arBody),
            ],
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
