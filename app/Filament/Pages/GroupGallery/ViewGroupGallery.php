<?php
namespace App\Filament\Pages\GroupGallery;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewGroupGallery extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-group-gallery/{record}/view';
    

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
        return $form->schema(\App\Filament\Pages\ManageGroupGallery::getGalleryItemFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageGroupGallery::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
