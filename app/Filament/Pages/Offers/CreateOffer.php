<?php
namespace App\Filament\Pages\Offers;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateOffer extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-offers/create';
    

    public ?array $data = [];

    public function mount(): void
    {
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('manageOfferDetails')
                ->label('Manage Offer Details')
                ->icon('heroicon-o-document-text')
                ->disabled()
                ->tooltip('Please create the offer first to manage its details.')
        ];
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageOffers::getOfferFormSchema())->statePath('data');
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageOffers::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageOffers::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
