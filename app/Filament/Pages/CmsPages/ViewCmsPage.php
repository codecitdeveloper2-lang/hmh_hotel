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
                'coming_soon_sections' => $decodedBody['coming_soon_sections'] ?? [],
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
