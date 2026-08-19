<?php
namespace App\Filament\Pages\Hotels\FAQs;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateFaq extends Page implements HasForms
{
    use HasHotelTabs;
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/faqs/create';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public ?array $data = [];

    public $record;

    public function mount($record): void
    {
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $this->form->fill([
            'hotel' => (int) $this->record,
        ]);
    }



    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\Hotels\FAQs\ListFaqs::getFaqFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        \App\Models\FaqItem::create([
            'property_id' => $this->record,
            'question' => ['en' => $data['question'] ?? ''],
            'answer' => ['en' => $data['answer'] ?? ''],
            'sort_order' => $data['display_order'] ?? 0,
        ]);
        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\FAQs\ListFaqs::getUrl(['record' => $this->record]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\FAQs\ListFaqs::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
