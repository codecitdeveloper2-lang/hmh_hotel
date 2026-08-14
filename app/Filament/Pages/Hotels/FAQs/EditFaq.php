<?php
namespace App\Filament\Pages\Hotels\FAQs;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditFaq extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/faqs/{faq_id}/edit';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public $record;
    public $faq_id;
    public ?array $data = [];

    public function mount($record, $faq_id = null): void
    {
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

    public function getSubNavigation(): array
    {
        return [];
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\Hotels\FAQs\ListFaqs::getFaqFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $faq = \App\Models\FaqItem::find($this->faq_id);
        if ($faq) {
            $question = is_array($faq->question) ? $faq->question : ['en' => $faq->question];
            $question['en'] = $data['question'] ?? '';
            $answer = is_array($faq->answer) ? $faq->answer : ['en' => $faq->answer];
            $answer['en'] = $data['answer'] ?? '';
            $faq->update([
                'question' => $question,
                'answer' => $answer,
                'sort_order' => $data['display_order'] ?? $faq->sort_order,
            ]);
        }
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\FAQs\ListFaqs::getUrl(['record' => $this->record]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\FAQs\ListFaqs::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
