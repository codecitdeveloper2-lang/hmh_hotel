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
        $mockData = \App\Filament\Pages\Hotels\FAQs\ListFaqs::getMockFaqs();
        $this->data = $mockData[$this->faq_id] ?? [];
        $this->form->fill($this->data);
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
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\FAQs\ListFaqs::getUrl(['record' => $this->record]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\FAQs\ListFaqs::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
