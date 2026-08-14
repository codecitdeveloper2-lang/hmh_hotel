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
        $this->form->fill();
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
        return $form
            ->schema(\App\Filament\Pages\ManageOffers::getOfferFormSchema())
            ->model(Offer::class)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $offer = new \App\Models\Offer();
        $offer->name = $data['title'] ?? '';
        $offer->description = $data['short_description'] ?? null;
        $offer->hotel = $data['hotel'] ?? null;
        $offer->offer_type = $data['offer_type'] ?? null;
        $offer->status = $data['status'] ?? 'Active';
        $offer->is_active = ($offer->status === 'Active');
        $offer->valid_from = $data['valid_from'] ?? null;
        $offer->valid_to = $data['valid_until'] ?? null;
        $offer->booking_period = $data['booking_period'] ?? null;
        $offer->banner_image = $data['banner_image'] ?? null;
        $offer->meta_title = $data['meta_title'] ?? null;
        $offer->meta_description = $data['meta_description'] ?? null;
        $offer->meta_keywords = $data['meta_keywords'] ?? null;
        
        $offer->save();

        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageOffers::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageOffers::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
