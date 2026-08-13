<?php
namespace App\Filament\Pages\Destinations;

use App\Models\Destination;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Contracts\Support\Htmlable;

class ViewDestination extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.destinations.view-destination';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-destinations/{record}/view';

    public $record;
    public string $activeLocale = 'en';

    /** Destination model instance */
    public ?Destination $destination = null;

    public function mount($record): void
    {
        $this->record      = $record;
        $this->destination = Destination::with(['cities', 'seoMetadata', 'media'])->findOrFail($record);
    }

    /** Switch locale when toggle is clicked */
    public function switchLocale(string $locale): void
    {
        $this->activeLocale = $locale;
    }

    public function getBackUrl(): string
    {
        return \App\Filament\Pages\ManageDestinations::getUrl();
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    /** Helper: get translated value for the active locale only */
    public function trans(string $attribute): string
    {
        if (!$this->destination) return '';

        $value = $this->destination->getTranslation($attribute, $this->activeLocale, false);

        return filled($value) ? $value : '';
    }
}
