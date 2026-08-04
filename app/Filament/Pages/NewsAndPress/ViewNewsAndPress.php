<?php
namespace App\Filament\Pages\NewsAndPress;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewNewsAndPress extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-news-and-press/{record}/view';
    

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $mockData = \App\Filament\Pages\ManageNewsAndPress::getMockArticles();
        $this->data = $mockData[$this->record] ?? [];
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageNewsAndPress::getArticleFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageNewsAndPress::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
