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
        $item = \App\Models\GalleryItem::findOrFail($this->record);
        $this->form->fill([
            'title' => $item->caption,
            'slug' => \Illuminate\Support\Str::slug($item->caption ?? ''),
            'display_order' => $item->sort_order,
            'status' => 'Published',
        ]);
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageGroupGallery::getGalleryItemFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $item = \App\Models\GalleryItem::findOrFail($this->record);
        $item->update([
            'caption' => $data['title'] ?? 'Untitled',
            'sort_order' => $data['display_order'] ?? 0,
        ]);
        
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageGroupGallery::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageGroupGallery::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
