<?php
namespace App\Filament\Pages\Members;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewMember extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-members/{record}/view';
    

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $mockData = \App\Filament\Pages\ManageMembers::getMockMembers();
        $this->data = $mockData[$this->record] ?? [];
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageMembers::getViewMemberFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageMembers::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
