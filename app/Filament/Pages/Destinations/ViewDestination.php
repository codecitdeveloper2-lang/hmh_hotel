<?php
namespace App\Filament\Pages\Destinations;

use App\Models\Destination;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class ViewDestination extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-destinations/{record}/view';
    

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $this->data = \App\Filament\Pages\ManageDestinations::getDestinationFormData($this->record);
        abort_if(empty($this->data), 404);

        $this->form->fill($this->data);
    }

    public function form($form)
    {
        return $form
            ->schema(\App\Filament\Pages\ManageDestinations::getDestinationFormSchema())
            ->model(Destination::class)
            ->record($this->record ? Destination::find($this->record) : null)
            ->disabled()
            ->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageDestinations::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
