<?php
namespace App\Filament\Pages\GroupGallery;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditGroupGallery extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-group-gallery/{record}/edit';
    

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $mockData = \App\Filament\Pages\ManageGroupGallery::getMockGalleryItems();
        $this->data = $mockData[$this->record] ?? [];
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageGroupGallery::getGalleryItemFormSchema())->statePath('data');
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageGroupGallery::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageGroupGallery::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
