<?php
namespace App\Filament\Pages\Offers;

use App\Models\Offer;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Contracts\Support\Htmlable;

class ViewOffer extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.offers.view-offer';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-offers/{record}/view';

    public $record;
    public string $activeLocale = 'en';

    /** Offer model instance */
    public ?Offer $offer = null;

    public function mount($record): void
    {
        $this->record = $record;
        $this->offer  = Offer::findOrFail($record);
    }

    /** Switch locale when toggle is clicked */
    public function switchLocale(string $locale): void
    {
        $this->activeLocale = $locale;
        // Data is now handled dynamically from the model in the blade view
    }

    public function getBackUrl(): string
    {
        return \App\Filament\Pages\ManageOffers::getUrl();
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    /** Helper: get translated value for the active locale only */
    public function trans(string $attribute): string
    {
        if (!$this->offer) return '';

        $value = $this->offer->getTranslation($attribute, $this->activeLocale, false);

        return filled($value) ? $value : '';
    }
}
