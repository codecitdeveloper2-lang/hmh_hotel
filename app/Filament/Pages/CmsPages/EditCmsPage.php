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

            $enBodyRaw = $page->getTranslation('body', 'en', false);
            $enBody = is_string($enBodyRaw) ? json_decode($enBodyRaw, true) : ($enBodyRaw ?? []);
            if (!is_array($enBody)) {
                $enBody = ['content' => $enBodyRaw];
            }

            $arBodyRaw = $page->getTranslation('body', 'ar', false);
            $arBody = is_string($arBodyRaw) ? json_decode($arBodyRaw, true) : ($arBodyRaw ?? []);
            if (!is_array($arBody)) {
                $arBody = [];
            }

            // Helper for simple text field
            $val = function($field, $default = '') use ($enBody, $arBody) {
                $enVal = $enBody[$field] ?? $default;
                $arVal = $arBody[$field] ?? '';
                if (is_array($enVal)) {
                    return [
                        'en' => $enVal['en'] ?? $default,
                        'ar' => $arVal ?: ($enVal['ar'] ?? ''),
                    ];
                }
                return [
                    'en' => (string)($enVal ?? ''),
                    'ar' => (string)($arVal ?? ''),
                ];
            };

            // Banner slides
            $enSlides = $enBody['banner_slides'] ?? [];
            $arSlides = $arBody['banner_slides'] ?? [];
            $maxSlides = max(count($enSlides), count($arSlides));
            $bannerSlides = [];
            for ($i = 0; $i < $maxSlides; $i++) {
                $enS = $enSlides[$i] ?? [];
                $arS = $arSlides[$i] ?? [];
                $bannerSlides[] = [
                    'image' => $enS['image'] ?? $arS['image'] ?? null,
                    'title' => [
                        'en' => is_array($enS['title'] ?? null) ? ($enS['title']['en'] ?? '') : ($enS['title'] ?? ''),
                        'ar' => is_array($arS['title'] ?? null) ? ($arS['title']['ar'] ?? '') : ($arS['title'] ?? ($enS['title']['ar'] ?? '')),
                    ],
                    'subtitle' => [
                        'en' => is_array($enS['subtitle'] ?? null) ? ($enS['subtitle']['en'] ?? '') : ($enS['subtitle'] ?? ''),
                        'ar' => is_array($arS['subtitle'] ?? null) ? ($arS['subtitle']['ar'] ?? '') : ($arS['subtitle'] ?? ($enS['subtitle']['ar'] ?? '')),
                    ],
                ];
            }

            // Coming soon sections
            $enCs = $enBody['coming_soon_sections'] ?? [];
            $arCs = $arBody['coming_soon_sections'] ?? [];
            $maxCs = max(count($enCs), count($arCs));
            $comingSoonSections = [];
            for ($i = 0; $i < $maxCs; $i++) {
                $e = $enCs[$i] ?? [];
                $a = $arCs[$i] ?? [];
                $comingSoonSections[] = [
                    'image' => $e['image'] ?? $a['image'] ?? null,
                    'title' => [
                        'en' => is_array($e['title'] ?? null) ? ($e['title']['en'] ?? '') : ($e['title'] ?? 'Coming Soon'),
                        'ar' => is_array($a['title'] ?? null) ? ($a['title']['ar'] ?? '') : ($a['title'] ?? ($e['title']['ar'] ?? '')),
                    ],
                    'hotel_name' => [
                        'en' => is_array($e['hotel_name'] ?? null) ? ($e['hotel_name']['en'] ?? '') : ($e['hotel_name'] ?? ''),
                        'ar' => is_array($a['hotel_name'] ?? null) ? ($a['hotel_name']['ar'] ?? '') : ($a['hotel_name'] ?? ($e['hotel_name']['ar'] ?? '')),
                    ],
                    'description' => [
                        'en' => is_array($e['description'] ?? null) ? ($e['description']['en'] ?? '') : ($e['description'] ?? ''),
                        'ar' => is_array($a['description'] ?? null) ? ($a['description']['ar'] ?? '') : ($a['description'] ?? ($e['description']['ar'] ?? '')),
                    ],
                ];
            }

            // Responsibilities repeater
            $enResp = $enBody['responsibilities_list'] ?? [];
            $arResp = $arBody['responsibilities_list'] ?? [];
            $maxResp = max(count($enResp), count($arResp));
            $responsibilitiesList = [];
            for ($i = 0; $i < $maxResp; $i++) {
                $e = $enResp[$i] ?? [];
                $a = $arResp[$i] ?? [];
                $responsibilitiesList[] = [
                    'image' => $e['image'] ?? $a['image'] ?? null,
                    'title' => [
                        'en' => is_array($e['title'] ?? null) ? ($e['title']['en'] ?? '') : ($e['title'] ?? ''),
                        'ar' => is_array($a['title'] ?? null) ? ($a['title']['ar'] ?? '') : ($a['title'] ?? ($e['title']['ar'] ?? '')),
                    ],
                    'description' => [
                        'en' => is_array($e['description'] ?? null) ? ($e['description']['en'] ?? '') : ($e['description'] ?? ''),
                        'ar' => is_array($a['description'] ?? null) ? ($a['description']['ar'] ?? '') : ($a['description'] ?? ($e['description']['ar'] ?? '')),
                    ],
                ];
            }

            // Partners repeater
            $enPart = $enBody['partners_list'] ?? [];
            $arPart = $arBody['partners_list'] ?? [];
            $maxPart = max(count($enPart), count($arPart));
            $partnersList = [];
            for ($i = 0; $i < $maxPart; $i++) {
                $e = $enPart[$i] ?? [];
                $a = $arPart[$i] ?? [];
                $partnersList[] = [
                    'image' => $e['image'] ?? $a['image'] ?? null,
                    'banner_image' => $e['banner_image'] ?? $a['banner_image'] ?? null,
                    'link' => $e['link'] ?? $a['link'] ?? null,
                    'name' => [
                        'en' => is_array($e['name'] ?? null) ? ($e['name']['en'] ?? '') : ($e['name'] ?? ''),
                        'ar' => is_array($a['name'] ?? null) ? ($a['name']['ar'] ?? '') : ($a['name'] ?? ($e['name']['ar'] ?? '')),
                    ],
                    'description' => [
                        'en' => is_array($e['description'] ?? null) ? ($e['description']['en'] ?? '') : ($e['description'] ?? ''),
                        'ar' => is_array($a['description'] ?? null) ? ($a['description']['ar'] ?? '') : ($a['description'] ?? ($e['description']['ar'] ?? '')),
                    ],
                ];
            }

            // Team members repeater
            $enTeam = $enBody['team_members_list'] ?? [];
            $arTeam = $arBody['team_members_list'] ?? [];
            $maxTeam = max(count($enTeam), count($arTeam));
            $teamMembersList = [];
            for ($i = 0; $i < $maxTeam; $i++) {
                $e = $enTeam[$i] ?? [];
                $a = $arTeam[$i] ?? [];
                $teamMembersList[] = [
                    'image' => $e['image'] ?? $a['image'] ?? null,
                    'name' => [
                        'en' => is_array($e['name'] ?? null) ? ($e['name']['en'] ?? '') : ($e['name'] ?? ''),
                        'ar' => is_array($a['name'] ?? null) ? ($a['name']['ar'] ?? '') : ($a['name'] ?? ($e['name']['ar'] ?? '')),
                    ],
                    'position' => [
                        'en' => is_array($e['position'] ?? null) ? ($e['position']['en'] ?? '') : ($e['position'] ?? ''),
                        'ar' => is_array($a['position'] ?? null) ? ($a['position']['ar'] ?? '') : ($a['position'] ?? ($e['position']['ar'] ?? '')),
                    ],
                    'description' => [
                        'en' => is_array($e['description'] ?? null) ? ($e['description']['en'] ?? '') : ($e['description'] ?? ''),
                        'ar' => is_array($a['description'] ?? null) ? ($a['description']['ar'] ?? '') : ($a['description'] ?? ($e['description']['ar'] ?? '')),
                    ],
                ];
            }

            // History timeline repeater
            $enHist = $enBody['history_timeline'] ?? [];
            $arHist = $arBody['history_timeline'] ?? [];
            $maxHist = max(count($enHist), count($arHist));
            $historyTimeline = [];
            for ($i = 0; $i < $maxHist; $i++) {
                $e = $enHist[$i] ?? [];
                $a = $arHist[$i] ?? [];
                $historyTimeline[] = [
                    'year' => $e['year'] ?? $a['year'] ?? '',
                    'image' => $e['image'] ?? $a['image'] ?? null,
                    'description' => [
                        'en' => is_array($e['description'] ?? null) ? ($e['description']['en'] ?? '') : ($e['description'] ?? ''),
                        'ar' => is_array($a['description'] ?? null) ? ($a['description']['ar'] ?? '') : ($a['description'] ?? ($e['description']['ar'] ?? '')),
                    ],
                ];
            }

            // Privacy accordion repeater
            $enPriv = $enBody['privacy_accordion'] ?? [];
            $arPriv = $arBody['privacy_accordion'] ?? [];
            $maxPriv = max(count($enPriv), count($arPriv));
            $privacyAccordion = [];
            for ($i = 0; $i < $maxPriv; $i++) {
                $e = $enPriv[$i] ?? [];
                $a = $arPriv[$i] ?? [];
                $privacyAccordion[] = [
                    'title' => [
                        'en' => is_array($e['title'] ?? null) ? ($e['title']['en'] ?? '') : ($e['title'] ?? ''),
                        'ar' => is_array($a['title'] ?? null) ? ($a['title']['ar'] ?? '') : ($a['title'] ?? ($e['title']['ar'] ?? '')),
                    ],
                    'description' => [
                        'en' => is_array($e['description'] ?? null) ? ($e['description']['en'] ?? '') : ($e['description'] ?? ''),
                        'ar' => is_array($a['description'] ?? null) ? ($a['description']['ar'] ?? '') : ($a['description'] ?? ($e['description']['ar'] ?? '')),
                    ],
                ];
            }

            // Terms accordion repeater
            $enTerms = $enBody['terms_accordion'] ?? [];
            $arTerms = $arBody['terms_accordion'] ?? [];
            $maxTerms = max(count($enTerms), count($arTerms));
            $termsAccordion = [];
            for ($i = 0; $i < $maxTerms; $i++) {
                $e = $enTerms[$i] ?? [];
                $a = $arTerms[$i] ?? [];
                $termsAccordion[] = [
                    'title' => [
                        'en' => is_array($e['title'] ?? null) ? ($e['title']['en'] ?? '') : ($e['title'] ?? ''),
                        'ar' => is_array($a['title'] ?? null) ? ($a['title']['ar'] ?? '') : ($a['title'] ?? ($e['title']['ar'] ?? '')),
                    ],
                    'description' => [
                        'en' => is_array($e['description'] ?? null) ? ($e['description']['en'] ?? '') : ($e['description'] ?? ''),
                        'ar' => is_array($a['description'] ?? null) ? ($a['description']['ar'] ?? '') : ($a['description'] ?? ($e['description']['ar'] ?? '')),
                    ],
                ];
            }

            // Press releases repeater
            $enPress = $enBody['press_releases_list'] ?? [];
            $arPress = $arBody['press_releases_list'] ?? [];
            $maxPress = max(count($enPress), count($arPress));
            $pressReleasesList = [];
            for ($i = 0; $i < $maxPress; $i++) {
                $e = $enPress[$i] ?? [];
                $a = $arPress[$i] ?? [];
                $pressReleasesList[] = [
                    'date' => $e['date'] ?? $a['date'] ?? '',
                    'category' => $e['category'] ?? $a['category'] ?? '',
                    'image' => $e['image'] ?? $a['image'] ?? null,
                    'link' => $e['link'] ?? $a['link'] ?? '',
                    'title' => [
                        'en' => is_array($e['title'] ?? null) ? ($e['title']['en'] ?? '') : ($e['title'] ?? ''),
                        'ar' => is_array($a['title'] ?? null) ? ($a['title']['ar'] ?? '') : ($a['title'] ?? ($e['title']['ar'] ?? '')),
                    ],
                    'description' => [
                        'en' => is_array($e['description'] ?? null) ? ($e['description']['en'] ?? '') : ($e['description'] ?? ''),
                        'ar' => is_array($a['description'] ?? null) ? ($a['description']['ar'] ?? '') : ($a['description'] ?? ($e['description']['ar'] ?? '')),
                    ],
                ];
            }

            // Careers repeater
            $enCar = $enBody['careers_list'] ?? [];
            $arCar = $arBody['careers_list'] ?? [];
            $maxCar = max(count($enCar), count($arCar));
            $careersList = [];
            for ($i = 0; $i < $maxCar; $i++) {
                $e = $enCar[$i] ?? [];
                $a = $arCar[$i] ?? [];
                $careersList[] = [
                    'apply_link' => $e['apply_link'] ?? $a['apply_link'] ?? '',
                    'type' => $e['type'] ?? $a['type'] ?? 'Full Time',
                    'title' => [
                        'en' => is_array($e['title'] ?? null) ? ($e['title']['en'] ?? '') : ($e['title'] ?? ''),
                        'ar' => is_array($a['title'] ?? null) ? ($a['title']['ar'] ?? '') : ($a['title'] ?? ($e['title']['ar'] ?? '')),
                    ],
                    'department' => [
                        'en' => is_array($e['department'] ?? null) ? ($e['department']['en'] ?? '') : ($e['department'] ?? ''),
                        'ar' => is_array($a['department'] ?? null) ? ($a['department']['ar'] ?? '') : ($a['department'] ?? ($e['department']['ar'] ?? '')),
                    ],
                    'location' => [
                        'en' => is_array($e['location'] ?? null) ? ($e['location']['en'] ?? '') : ($e['location'] ?? ''),
                        'ar' => is_array($a['location'] ?? null) ? ($a['location']['ar'] ?? '') : ($a['location'] ?? ($e['location']['ar'] ?? '')),
                    ],
                    'description' => [
                        'en' => is_array($e['description'] ?? null) ? ($e['description']['en'] ?? '') : ($e['description'] ?? ''),
                        'ar' => is_array($a['description'] ?? null) ? ($a['description']['ar'] ?? '') : ($a['description'] ?? ($e['description']['ar'] ?? '')),
                    ],
                ];
            }

            // Locations list repeater
            $enLoc = $enBody['locations_list'] ?? [];
            $arLoc = $arBody['locations_list'] ?? [];
            $maxLoc = max(count($enLoc), count($arLoc));
            $locationsList = [];
            for ($i = 0; $i < $maxLoc; $i++) {
                $e = $enLoc[$i] ?? [];
                $a = $arLoc[$i] ?? [];
                $locationsList[] = [
                    'phone' => $e['phone'] ?? $a['phone'] ?? '',
                    'email' => $e['email'] ?? $a['email'] ?? '',
                    'image' => $e['image'] ?? $a['image'] ?? null,
                    'latitude' => $e['latitude'] ?? $a['latitude'] ?? '',
                    'longitude' => $e['longitude'] ?? $a['longitude'] ?? '',
                    'title' => [
                        'en' => is_array($e['title'] ?? null) ? ($e['title']['en'] ?? '') : ($e['title'] ?? ''),
                        'ar' => is_array($a['title'] ?? null) ? ($a['title']['ar'] ?? '') : ($a['title'] ?? ($e['title']['ar'] ?? '')),
                    ],
                    'city' => [
                        'en' => is_array($e['city'] ?? null) ? ($e['city']['en'] ?? '') : ($e['city'] ?? ''),
                        'ar' => is_array($a['city'] ?? null) ? ($a['city']['ar'] ?? '') : ($a['city'] ?? ($e['city']['ar'] ?? '')),
                    ],
                    'address' => [
                        'en' => is_array($e['address'] ?? null) ? ($e['address']['en'] ?? '') : ($e['address'] ?? ''),
                        'ar' => is_array($a['address'] ?? null) ? ($a['address']['ar'] ?? '') : ($a['address'] ?? ($e['address']['ar'] ?? '')),
                    ],
                ];
            }

            // Hotel contacts repeater
            $enHot = $enBody['hotel_contacts'] ?? [];
            $arHot = $arBody['hotel_contacts'] ?? [];
            $maxHot = max(count($enHot), count($arHot));
            $hotelContacts = [];
            for ($i = 0; $i < $maxHot; $i++) {
                $e = $enHot[$i] ?? [];
                $a = $arHot[$i] ?? [];
                $hotelContacts[] = [
                    'email' => $e['email'] ?? $a['email'] ?? '',
                    'phone' => $e['phone'] ?? $a['phone'] ?? '',
                    'hotel_name' => [
                        'en' => is_array($e['hotel_name'] ?? null) ? ($e['hotel_name']['en'] ?? '') : ($e['hotel_name'] ?? ''),
                        'ar' => is_array($a['hotel_name'] ?? null) ? ($a['hotel_name']['ar'] ?? '') : ($a['hotel_name'] ?? ($e['hotel_name']['ar'] ?? '')),
                    ],
                ];
            }

            // Brands list repeater
            $enBr = $enBody['brands_list'] ?? [];
            $arBr = $arBody['brands_list'] ?? [];
            $maxBr = max(count($enBr), count($arBr));
            $brandsList = [];
            for ($i = 0; $i < $maxBr; $i++) {
                $e = $enBr[$i] ?? [];
                $a = $arBr[$i] ?? [];
                $brandsList[] = [
                    'image' => $e['image'] ?? $a['image'] ?? null,
                    'logo' => $e['logo'] ?? $a['logo'] ?? null,
                    'link' => $e['link'] ?? $a['link'] ?? '',
                    'name' => [
                        'en' => is_array($e['name'] ?? null) ? ($e['name']['en'] ?? '') : ($e['name'] ?? ''),
                        'ar' => is_array($a['name'] ?? null) ? ($a['name']['ar'] ?? '') : ($a['name'] ?? ($e['name']['ar'] ?? '')),
                    ],
                    'tagline' => [
                        'en' => is_array($e['tagline'] ?? null) ? ($e['tagline']['en'] ?? '') : ($e['tagline'] ?? ''),
                        'ar' => is_array($a['tagline'] ?? null) ? ($a['tagline']['ar'] ?? '') : ($a['tagline'] ?? ($e['tagline']['ar'] ?? '')),
                    ],
                    'description' => [
                        'en' => is_array($e['description'] ?? null) ? ($e['description']['en'] ?? '') : ($e['description'] ?? ''),
                        'ar' => is_array($a['description'] ?? null) ? ($a['description']['ar'] ?? '') : ($a['description'] ?? ($e['description']['ar'] ?? '')),
                    ],
                ];
            }

            // Services list repeater
            $enSrv = $enBody['services_list'] ?? [];
            $arSrv = $arBody['services_list'] ?? [];
            $maxSrv = max(count($enSrv), count($arSrv));
            $servicesList = [];
            for ($i = 0; $i < $maxSrv; $i++) {
                $e = $enSrv[$i] ?? [];
                $a = $arSrv[$i] ?? [];
                $servicesList[] = [
                    'icon' => $e['icon'] ?? $a['icon'] ?? null,
                    'title' => [
                        'en' => is_array($e['title'] ?? null) ? ($e['title']['en'] ?? '') : ($e['title'] ?? ''),
                        'ar' => is_array($a['title'] ?? null) ? ($a['title']['ar'] ?? '') : ($a['title'] ?? ($e['title']['ar'] ?? '')),
                    ],
                ];
            }

            $data = [
                'activeLocale' => 'en',
                'title' => [
                    'en' => $page->getTranslation('title', 'en', false) ?: (is_array($page->title) ? ($page->title['en'] ?? '') : ($page->title ?? '')),
                    'ar' => $page->getTranslation('title', 'ar', false) ?: (is_array($page->title) ? ($page->title['ar'] ?? '') : ''),
                ],
                'page_type' => $formPageType,
                'slug' => $page->slug,
                'status' => $page->is_active ? 'Published' : 'Draft',
                'display_order' => $enBody['display_order'] ?? null,
                'banner_images' => $enBody['banner_images'] ?? [],
                'banner_slides' => $bannerSlides,
                'content_title' => $val('content_title'),
                'content' => $val('content'),
                'cta_text' => $val('cta_text'),
                'cta_link' => $enBody['cta_link'] ?? '',
                'intro_subtitle' => $val('intro_subtitle'),
                'intro_title' => $val('intro_title'),
                'intro_text' => $val('intro_text'),
                'expansion_image' => $enBody['expansion_image'] ?? '',
                'expansion_text' => $val('expansion_text'),
                'our_vision_text' => $val('our_vision_text'),
                'our_vision_image' => $enBody['our_vision_image'] ?? '',
                'our_mission_text' => $val('our_mission_text'),
                'our_mission_image' => $enBody['our_mission_image'] ?? '',
                'our_values' => $val('our_values'),
                'our_culture' => $val('our_culture'),
                'our_promise' => $val('our_promise'),
                'responsibilities_list' => $responsibilitiesList,
                'partners_list' => $partnersList,
                'team_members_list' => $teamMembersList,
                'history_timeline' => $historyTimeline,
                'coming_soon_sections' => $comingSoonSections,
                'categories' => $enBody['categories'] ?? [],
                'corp_amman_images' => $enBody['corp_amman_images'] ?? [],
                'coral_beach_sharjah_images' => $enBody['coral_beach_sharjah_images'] ?? [],
                'bahi_ajman_palace_images' => $enBody['bahi_ajman_palace_images'] ?? [],
                'ecos_dubai_images' => $enBody['ecos_dubai_images'] ?? [],
                'coral_dubai_deira_images' => $enBody['coral_dubai_deira_images'] ?? [],
                'coral_jubail_images' => $enBody['coral_jubail_images'] ?? [],
                'gallery_items' => $enBody['gallery_items'] ?? [],
                'future_slider_images' => $enBody['future_slider_images'] ?? [],
                'value_proposition_title' => $val('value_proposition_title'),
                'value_proposition_text' => $val('value_proposition_text'),
                'brands_list' => $brandsList,
                'services_title' => $val('services_title'),
                'services_intro' => $val('services_intro'),
                'services_list' => $servicesList,
                'terms_accordion' => $termsAccordion,
                'privacy_accordion' => $privacyAccordion,
                'privacy_slider_images' => $enBody['privacy_slider_images'] ?? [],
                'terms_slider_images' => $enBody['terms_slider_images'] ?? [],
                'press_releases_list' => $pressReleasesList,
                'careers_list' => $careersList,
                'locations_list' => $locationsList,
                'hotel_contacts' => $hotelContacts,
                'central_phone' => $enBody['central_phone'] ?? '',
                'central_whatsapp' => $enBody['central_whatsapp'] ?? '',
                'central_email' => $enBody['central_email'] ?? '',
                'meta_title' => [
                    'en' => $page->getTranslation('meta_title', 'en', false) ?: (is_array($page->meta_title) ? ($page->meta_title['en'] ?? '') : ($page->meta_title ?? '')),
                    'ar' => $page->getTranslation('meta_title', 'ar', false) ?: (is_array($page->meta_title) ? ($page->meta_title['ar'] ?? '') : ''),
                ],
                'meta_description' => [
                    'en' => $page->getTranslation('meta_description', 'en', false) ?: (is_array($page->meta_description) ? ($page->meta_description['en'] ?? '') : ($page->meta_description ?? '')),
                    'ar' => $page->getTranslation('meta_description', 'ar', false) ?: (is_array($page->meta_description) ? ($page->meta_description['ar'] ?? '') : ''),
                ],
                'meta_keywords' => $val('meta_keywords'),
                'canonical_url' => $enBody['canonical_url'] ?? '',
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

            $existingEnBody = is_array($page->body) ? ($page->body['en'] ?? '') : $page->body;
            $decodedEnBody = json_decode((string)$existingEnBody, true) ?? [];
            if (!is_array($decodedEnBody)) $decodedEnBody = [];

            $titleEn = is_array($data['title'] ?? null) ? ($data['title']['en'] ?? '') : ($data['title'] ?? '');
            $titleAr = is_array($data['title'] ?? null) ? ($data['title']['ar'] ?? '') : '';

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

            $bannerImages = $data['banner_images'] ?? ($decodedEnBody['banner_images'] ?? []);
            if (!empty($bannerImages)) {
                $existingSlideImages = array_column($enBannerSlides, 'image');
                foreach ($bannerImages as $bImg) {
                    if (!in_array($bImg, $existingSlideImages)) {
                        $enBannerSlides[] = [
                            'image' => $bImg,
                            'title' => $titleEn,
                            'subtitle' => $getText('intro_subtitle', 'en') ?: 'Hospitality Management Holding',
                        ];
                        $arBannerSlides[] = [
                            'image' => $bImg,
                            'title' => !empty($titleAr) ? $titleAr : $titleEn,
                            'subtitle' => $getText('intro_subtitle', 'ar') ?: 'Hospitality Management Holding',
                        ];
                    }
                }
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

            // Responsibilities
            $enResponsibilities = [];
            $arResponsibilities = [];
            foreach ($data['responsibilities_list'] ?? [] as $r) {
                $rTitleEn = is_array($r['title'] ?? null) ? ($r['title']['en'] ?? '') : ($r['title'] ?? '');
                $rTitleAr = is_array($r['title'] ?? null) ? ($r['title']['ar'] ?? '') : '';
                $rDescEn = is_array($r['description'] ?? null) ? ($r['description']['en'] ?? '') : ($r['description'] ?? '');
                $rDescAr = is_array($r['description'] ?? null) ? ($r['description']['ar'] ?? '') : '';

                $enResponsibilities[] = [
                    'title' => $rTitleEn,
                    'image' => $r['image'] ?? null,
                    'description' => $rDescEn,
                ];
                $arResponsibilities[] = [
                    'title' => !empty($rTitleAr) ? $rTitleAr : $rTitleEn,
                    'image' => $r['image'] ?? null,
                    'description' => !empty($rDescAr) ? $rDescAr : $rDescEn,
                ];
            }

            // Partners
            $enPartners = [];
            $arPartners = [];
            foreach ($data['partners_list'] ?? [] as $p) {
                $pNameEn = is_array($p['name'] ?? null) ? ($p['name']['en'] ?? '') : ($p['name'] ?? '');
                $pNameAr = is_array($p['name'] ?? null) ? ($p['name']['ar'] ?? '') : '';
                $pDescEn = is_array($p['description'] ?? null) ? ($p['description']['en'] ?? '') : ($p['description'] ?? '');
                $pDescAr = is_array($p['description'] ?? null) ? ($p['description']['ar'] ?? '') : '';

                $enPartners[] = [
                    'name' => $pNameEn,
                    'image' => $p['image'] ?? null,
                    'banner_image' => $p['banner_image'] ?? null,
                    'link' => $p['link'] ?? null,
                    'description' => $pDescEn,
                ];
                $arPartners[] = [
                    'name' => !empty($pNameAr) ? $pNameAr : $pNameEn,
                    'image' => $p['image'] ?? null,
                    'banner_image' => $p['banner_image'] ?? null,
                    'link' => $p['link'] ?? null,
                    'description' => !empty($pDescAr) ? $pDescAr : $pDescEn,
                ];
            }

            // Team members
            $enTeamMembers = [];
            $arTeamMembers = [];
            foreach ($data['team_members_list'] ?? [] as $tm) {
                $tmNameEn = is_array($tm['name'] ?? null) ? ($tm['name']['en'] ?? '') : ($tm['name'] ?? '');
                $tmNameAr = is_array($tm['name'] ?? null) ? ($tm['name']['ar'] ?? '') : '';
                $tmPosEn = is_array($tm['position'] ?? null) ? ($tm['position']['en'] ?? '') : ($tm['position'] ?? '');
                $tmPosAr = is_array($tm['position'] ?? null) ? ($tm['position']['ar'] ?? '') : '';
                $tmDescEn = is_array($tm['description'] ?? null) ? ($tm['description']['en'] ?? '') : ($tm['description'] ?? '');
                $tmDescAr = is_array($tm['description'] ?? null) ? ($tm['description']['ar'] ?? '') : '';

                $enTeamMembers[] = [
                    'name' => $tmNameEn,
                    'position' => $tmPosEn,
                    'image' => $tm['image'] ?? null,
                    'description' => $tmDescEn,
                ];
                $arTeamMembers[] = [
                    'name' => !empty($tmNameAr) ? $tmNameAr : $tmNameEn,
                    'position' => !empty($tmPosAr) ? $tmPosAr : $tmPosEn,
                    'image' => $tm['image'] ?? null,
                    'description' => !empty($tmDescAr) ? $tmDescAr : $tmDescEn,
                ];
            }

            // History timeline
            $enTimeline = [];
            $arTimeline = [];
            foreach ($data['history_timeline'] ?? [] as $ht) {
                $htDescEn = is_array($ht['description'] ?? null) ? ($ht['description']['en'] ?? '') : ($ht['description'] ?? '');
                $htDescAr = is_array($ht['description'] ?? null) ? ($ht['description']['ar'] ?? '') : '';

                $enTimeline[] = [
                    'year' => $ht['year'] ?? '',
                    'image' => $ht['image'] ?? null,
                    'description' => $htDescEn,
                ];
                $arTimeline[] = [
                    'year' => $ht['year'] ?? '',
                    'image' => $ht['image'] ?? null,
                    'description' => !empty($htDescAr) ? $htDescAr : $htDescEn,
                ];
            }

            // Brands list
            $enBrands = [];
            $arBrands = [];
            foreach ($data['brands_list'] ?? [] as $br) {
                $bNameEn = is_array($br['name'] ?? null) ? ($br['name']['en'] ?? '') : ($br['name'] ?? '');
                $bNameAr = is_array($br['name'] ?? null) ? ($br['name']['ar'] ?? '') : '';
                $bTagEn = is_array($br['tagline'] ?? null) ? ($br['tagline']['en'] ?? '') : ($br['tagline'] ?? '');
                $bTagAr = is_array($br['tagline'] ?? null) ? ($br['tagline']['ar'] ?? '') : '';
                $bDescEn = is_array($br['description'] ?? null) ? ($br['description']['en'] ?? '') : ($br['description'] ?? '');
                $bDescAr = is_array($br['description'] ?? null) ? ($br['description']['ar'] ?? '') : '';

                $enBrands[] = [
                    'name' => $bNameEn,
                    'tagline' => $bTagEn,
                    'image' => $br['image'] ?? null,
                    'logo' => $br['logo'] ?? null,
                    'link' => $br['link'] ?? null,
                    'description' => $bDescEn,
                ];
                $arBrands[] = [
                    'name' => !empty($bNameAr) ? $bNameAr : $bNameEn,
                    'tagline' => !empty($bTagAr) ? $bTagAr : $bTagEn,
                    'image' => $br['image'] ?? null,
                    'logo' => $br['logo'] ?? null,
                    'link' => $br['link'] ?? null,
                    'description' => !empty($bDescAr) ? $bDescAr : $bDescEn,
                ];
            }

            // Services list
            $enServices = [];
            $arServices = [];
            foreach ($data['services_list'] ?? [] as $srv) {
                $sTitleEn = is_array($srv['title'] ?? null) ? ($srv['title']['en'] ?? '') : ($srv['title'] ?? '');
                $sTitleAr = is_array($srv['title'] ?? null) ? ($srv['title']['ar'] ?? '') : '';

                $enServices[] = [
                    'title' => $sTitleEn,
                    'icon' => $srv['icon'] ?? null,
                ];
                $arServices[] = [
                    'title' => !empty($sTitleAr) ? $sTitleAr : $sTitleEn,
                    'icon' => $srv['icon'] ?? null,
                ];
            }

            // Terms accordion
            $enTerms = [];
            $arTerms = [];
            foreach ($data['terms_accordion'] ?? [] as $ta) {
                $taTitleEn = is_array($ta['title'] ?? null) ? ($ta['title']['en'] ?? '') : ($ta['title'] ?? '');
                $taTitleAr = is_array($ta['title'] ?? null) ? ($ta['title']['ar'] ?? '') : '';
                $taDescEn = is_array($ta['description'] ?? null) ? ($ta['description']['en'] ?? '') : ($ta['description'] ?? '');
                $taDescAr = is_array($ta['description'] ?? null) ? ($ta['description']['ar'] ?? '') : '';

                $enTerms[] = [
                    'title' => $taTitleEn,
                    'description' => $taDescEn,
                ];
                $arTerms[] = [
                    'title' => !empty($taTitleAr) ? $taTitleAr : $taTitleEn,
                    'description' => !empty($taDescAr) ? $taDescAr : $taDescEn,
                ];
            }

            // Privacy accordion
            $enPrivacy = [];
            $arPrivacy = [];
            foreach ($data['privacy_accordion'] ?? [] as $pa) {
                $paTitleEn = is_array($pa['title'] ?? null) ? ($pa['title']['en'] ?? '') : ($pa['title'] ?? '');
                $paTitleAr = is_array($pa['title'] ?? null) ? ($pa['title']['ar'] ?? '') : '';
                $paDescEn = is_array($pa['description'] ?? null) ? ($pa['description']['en'] ?? '') : ($pa['description'] ?? '');
                $paDescAr = is_array($pa['description'] ?? null) ? ($pa['description']['ar'] ?? '') : '';

                $enPrivacy[] = [
                    'title' => $paTitleEn,
                    'description' => $paDescEn,
                ];
                $arPrivacy[] = [
                    'title' => !empty($paTitleAr) ? $paTitleAr : $paTitleEn,
                    'description' => !empty($paDescAr) ? $paDescAr : $paDescEn,
                ];
            }

            // Press releases
            $enPressReleases = [];
            $arPressReleases = [];
            foreach ($data['press_releases_list'] ?? [] as $pr) {
                $prTitleEn = is_array($pr['title'] ?? null) ? ($pr['title']['en'] ?? '') : ($pr['title'] ?? '');
                $prTitleAr = is_array($pr['title'] ?? null) ? ($pr['title']['ar'] ?? '') : '';
                $prDescEn = is_array($pr['description'] ?? null) ? ($pr['description']['en'] ?? '') : ($pr['description'] ?? '');
                $prDescAr = is_array($pr['description'] ?? null) ? ($pr['description']['ar'] ?? '') : '';

                $enPressReleases[] = [
                    'title' => $prTitleEn,
                    'date' => $pr['date'] ?? '',
                    'category' => $pr['category'] ?? '',
                    'image' => $pr['image'] ?? null,
                    'link' => $pr['link'] ?? '',
                    'description' => $prDescEn,
                ];
                $arPressReleases[] = [
                    'title' => !empty($prTitleAr) ? $prTitleAr : $prTitleEn,
                    'date' => $pr['date'] ?? '',
                    'category' => $pr['category'] ?? '',
                    'image' => $pr['image'] ?? null,
                    'link' => $pr['link'] ?? '',
                    'description' => !empty($prDescAr) ? $prDescAr : $prDescEn,
                ];
            }

            // Careers
            $enCareers = [];
            $arCareers = [];
            foreach ($data['careers_list'] ?? [] as $cr) {
                $crTitleEn = is_array($cr['title'] ?? null) ? ($cr['title']['en'] ?? '') : ($cr['title'] ?? '');
                $crTitleAr = is_array($cr['title'] ?? null) ? ($cr['title']['ar'] ?? '') : '';
                $crDeptEn = is_array($cr['department'] ?? null) ? ($cr['department']['en'] ?? '') : ($cr['department'] ?? '');
                $crDeptAr = is_array($cr['department'] ?? null) ? ($cr['department']['ar'] ?? '') : '';
                $crLocEn = is_array($cr['location'] ?? null) ? ($cr['location']['en'] ?? '') : ($cr['location'] ?? '');
                $crLocAr = is_array($cr['location'] ?? null) ? ($cr['location']['ar'] ?? '') : '';
                $crDescEn = is_array($cr['description'] ?? null) ? ($cr['description']['en'] ?? '') : ($cr['description'] ?? '');
                $crDescAr = is_array($cr['description'] ?? null) ? ($cr['description']['ar'] ?? '') : '';

                $enCareers[] = [
                    'title' => $crTitleEn,
                    'department' => $crDeptEn,
                    'location' => $crLocEn,
                    'apply_link' => $cr['apply_link'] ?? '',
                    'type' => $cr['type'] ?? 'Full Time',
                    'description' => $crDescEn,
                ];
                $arCareers[] = [
                    'title' => !empty($crTitleAr) ? $crTitleAr : $crTitleEn,
                    'department' => !empty($crDeptAr) ? $crDeptAr : $crDeptEn,
                    'location' => !empty($crLocAr) ? $crLocAr : $crLocEn,
                    'apply_link' => $cr['apply_link'] ?? '',
                    'type' => $cr['type'] ?? 'Full Time',
                    'description' => !empty($crDescAr) ? $crDescAr : $crDescEn,
                ];
            }

            // Locations list
            $enLocations = [];
            $arLocations = [];
            foreach ($data['locations_list'] ?? [] as $loc) {
                $lTitleEn = is_array($loc['title'] ?? null) ? ($loc['title']['en'] ?? '') : ($loc['title'] ?? '');
                $lTitleAr = is_array($loc['title'] ?? null) ? ($loc['title']['ar'] ?? '') : '';
                $lCityEn = is_array($loc['city'] ?? null) ? ($loc['city']['en'] ?? '') : ($loc['city'] ?? '');
                $lCityAr = is_array($loc['city'] ?? null) ? ($loc['city']['ar'] ?? '') : '';
                $lAddrEn = is_array($loc['address'] ?? null) ? ($loc['address']['en'] ?? '') : ($loc['address'] ?? '');
                $lAddrAr = is_array($loc['address'] ?? null) ? ($loc['address']['ar'] ?? '') : '';

                $enLocations[] = [
                    'title' => $lTitleEn,
                    'city' => $lCityEn,
                    'address' => $lAddrEn,
                    'phone' => $loc['phone'] ?? '',
                    'email' => $loc['email'] ?? '',
                    'image' => $loc['image'] ?? null,
                    'latitude' => $loc['latitude'] ?? '',
                    'longitude' => $loc['longitude'] ?? '',
                ];
                $arLocations[] = [
                    'title' => !empty($lTitleAr) ? $lTitleAr : $lTitleEn,
                    'city' => !empty($lCityAr) ? $lCityAr : $lCityEn,
                    'address' => !empty($lAddrAr) ? $lAddrAr : $lAddrEn,
                    'phone' => $loc['phone'] ?? '',
                    'email' => $loc['email'] ?? '',
                    'image' => $loc['image'] ?? null,
                    'latitude' => $loc['latitude'] ?? '',
                    'longitude' => $loc['longitude'] ?? '',
                ];
            }

            // Hotel contacts
            $enHotelContacts = [];
            $arHotelContacts = [];
            foreach ($data['hotel_contacts'] ?? [] as $hc) {
                $hNameEn = is_array($hc['hotel_name'] ?? null) ? ($hc['hotel_name']['en'] ?? '') : ($hc['hotel_name'] ?? '');
                $hNameAr = is_array($hc['hotel_name'] ?? null) ? ($hc['hotel_name']['ar'] ?? '') : '';

                $enHotelContacts[] = [
                    'hotel_name' => $hNameEn,
                    'email' => $hc['email'] ?? '',
                    'phone' => $hc['phone'] ?? '',
                ];
                $arHotelContacts[] = [
                    'hotel_name' => !empty($hNameAr) ? $hNameAr : $hNameEn,
                    'email' => $hc['email'] ?? '',
                    'phone' => $hc['phone'] ?? '',
                ];
            }

            $enBody = [
                'display_order' => $data['display_order'] ?? null,
                'banner_images' => $bannerImages,
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
                'responsibilities_list' => $enResponsibilities,
                'partners_list' => $enPartners,
                'team_members_list' => $enTeamMembers,
                'history_timeline' => $enTimeline,
                'coming_soon_sections' => $enComingSoon,
                'categories' => $data['categories'] ?? ($decodedEnBody['categories'] ?? []),
                'corp_amman_images' => $data['corp_amman_images'] ?? ($decodedEnBody['corp_amman_images'] ?? []),
                'coral_beach_sharjah_images' => $data['coral_beach_sharjah_images'] ?? ($decodedEnBody['coral_beach_sharjah_images'] ?? []),
                'bahi_ajman_palace_images' => $data['bahi_ajman_palace_images'] ?? ($decodedEnBody['bahi_ajman_palace_images'] ?? []),
                'ecos_dubai_images' => $data['ecos_dubai_images'] ?? ($decodedEnBody['ecos_dubai_images'] ?? []),
                'coral_dubai_deira_images' => $data['coral_dubai_deira_images'] ?? ($decodedEnBody['coral_dubai_deira_images'] ?? []),
                'coral_jubail_images' => $data['coral_jubail_images'] ?? ($decodedEnBody['coral_jubail_images'] ?? []),
                'gallery_items' => $data['gallery_items'] ?? ($decodedEnBody['gallery_items'] ?? []),
                'future_slider_images' => $data['future_slider_images'] ?? ($decodedEnBody['future_slider_images'] ?? []),
                'value_proposition_title' => $getText('value_proposition_title', 'en'),
                'value_proposition_text' => $getText('value_proposition_text', 'en'),
                'brands_list' => $enBrands,
                'services_title' => $getText('services_title', 'en'),
                'services_intro' => $getText('services_intro', 'en'),
                'services_list' => $enServices,
                'terms_accordion' => $enTerms,
                'privacy_accordion' => $enPrivacy,
                'privacy_slider_images' => $data['privacy_slider_images'] ?? ($decodedEnBody['privacy_slider_images'] ?? []),
                'terms_slider_images' => $data['terms_slider_images'] ?? ($decodedEnBody['terms_slider_images'] ?? []),
                'press_releases_list' => $enPressReleases,
                'careers_list' => $enCareers,
                'locations_list' => $enLocations,
                'hotel_contacts' => $enHotelContacts,
                'central_phone' => $data['central_phone'] ?? '',
                'central_whatsapp' => $data['central_whatsapp'] ?? '',
                'central_email' => $data['central_email'] ?? '',
                'meta_keywords' => $getText('meta_keywords', 'en'),
                'canonical_url' => $data['canonical_url'] ?? '',
            ];

            $arBody = [
                'display_order' => $data['display_order'] ?? null,
                'banner_images' => $bannerImages,
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
                'responsibilities_list' => $arResponsibilities,
                'partners_list' => $arPartners,
                'team_members_list' => $arTeamMembers,
                'history_timeline' => $arTimeline,
                'coming_soon_sections' => $arComingSoon,
                'categories' => $data['categories'] ?? ($decodedEnBody['categories'] ?? []),
                'corp_amman_images' => $data['corp_amman_images'] ?? ($decodedEnBody['corp_amman_images'] ?? []),
                'coral_beach_sharjah_images' => $data['coral_beach_sharjah_images'] ?? ($decodedEnBody['coral_beach_sharjah_images'] ?? []),
                'bahi_ajman_palace_images' => $data['bahi_ajman_palace_images'] ?? ($decodedEnBody['bahi_ajman_palace_images'] ?? []),
                'ecos_dubai_images' => $data['ecos_dubai_images'] ?? ($decodedEnBody['ecos_dubai_images'] ?? []),
                'coral_dubai_deira_images' => $data['coral_dubai_deira_images'] ?? ($decodedEnBody['coral_dubai_deira_images'] ?? []),
                'coral_jubail_images' => $data['coral_jubail_images'] ?? ($decodedEnBody['coral_jubail_images'] ?? []),
                'gallery_items' => $data['gallery_items'] ?? ($decodedEnBody['gallery_items'] ?? []),
                'future_slider_images' => $data['future_slider_images'] ?? ($decodedEnBody['future_slider_images'] ?? []),
                'value_proposition_title' => $getText('value_proposition_title', 'ar'),
                'value_proposition_text' => $getText('value_proposition_text', 'ar'),
                'brands_list' => $arBrands,
                'services_title' => $getText('services_title', 'ar'),
                'services_intro' => $getText('services_intro', 'ar'),
                'services_list' => $arServices,
                'terms_accordion' => $arTerms,
                'privacy_accordion' => $arPrivacy,
                'privacy_slider_images' => $data['privacy_slider_images'] ?? ($decodedEnBody['privacy_slider_images'] ?? []),
                'terms_slider_images' => $data['terms_slider_images'] ?? ($decodedEnBody['terms_slider_images'] ?? []),
                'press_releases_list' => $arPressReleases,
                'careers_list' => $arCareers,
                'locations_list' => $arLocations,
                'hotel_contacts' => $arHotelContacts,
                'central_phone' => $data['central_phone'] ?? '',
                'central_whatsapp' => $data['central_whatsapp'] ?? '',
                'central_email' => $data['central_email'] ?? '',
                'meta_keywords' => $getText('meta_keywords', 'ar'),
                'canonical_url' => $data['canonical_url'] ?? '',
            ];

            $page->update([
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
