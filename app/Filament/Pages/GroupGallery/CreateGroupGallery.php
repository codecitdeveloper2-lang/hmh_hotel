<?php
namespace App\Filament\Pages\GroupGallery;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateGroupGallery extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-group-gallery/create';
    

    public ?array $data = [];

    public function mount(): void
    {
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageGroupGallery::getGalleryItemFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        \App\Models\GalleryItem::create([
            'caption' => $data['title'] ?? 'Untitled',
            'sort_order' => $data['display_order'] ?? 0,
        ]);
        
        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageGroupGallery::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageGroupGallery::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
