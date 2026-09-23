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
                'opera_grand_hotel_images' => $enBody['opera_grand_hotel_images'] ?? $enBody['coral_dubai_deira_images'] ?? [],
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
        return $form->schema(\App\Filament\Pages\ManageCmsPages::getPageFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageCmsPages::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
