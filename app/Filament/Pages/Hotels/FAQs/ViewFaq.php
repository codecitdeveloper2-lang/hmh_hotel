<?php
namespace App\Filament\Pages\Hotels\FAQs;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewFaq extends Page implements HasForms
{
    use HasHotelTabs;
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/faqs/{faq_id}/view';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public $record;
    public $faq_id;
    public ?array $data = [];

    public function mount($record, $faq_id = null): void
    {
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $this->faq_id = $faq_id;
        $faq = \App\Models\FaqItem::find($faq_id);
        if ($faq) {
            $this->form->fill([
                'hotel' => (int) $this->record,
                'question' => is_array($faq->question) ? ($faq->question['en'] ?? '') : $faq->question,
                'answer' => is_array($faq->answer) ? ($faq->answer['en'] ?? '') : $faq->answer,
                'display_order' => $faq->sort_order,
            ]);
        }
    }



    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\Hotels\FAQs\ListFaqs::getFaqFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\FAQs\ListFaqs::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
