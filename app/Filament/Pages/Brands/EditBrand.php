<?php
namespace App\Filament\Pages\Brands;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditBrand extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-brands/{record}/edit';
    

    public $record;
    public string $activeLocale = 'en';
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $property = \App\Models\Property::findOrFail($record);
        $data = $property->toArray();

        // Keep translatable fields as nested arrays (name.en, name.ar) for the locale-aware form
        // Filament will map name['en'] to the name.en field key automatically
        $this->form->fill($data);
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        $property = \App\Models\Property::find($this->record);
        return $property?->display_name ?? 'Edit Brand';
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageBrands::getBrandFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $validColumns = [
            'type', 'name', 'description', 'slug', 'star_rating',
            'address', 'city', 'country', 'latitude', 'longitude',
            'phone', 'email', 'travelclick_hotel_id', 'attractions_page_slug',
            'status', 'check_in_time', 'check_out_time',
            'meta_title', 'meta_description', 'is_active', 'sort_order',
            'tagline', 'google_location', 'location_title', 'contact_button_text',
            'contact_button_url', 'star_segment', 'logo', 'intro_text',
            'banner_title', 'banner_images', 'intro_subtitle', 'intro_title',
        ];
        $data = array_intersect_key($data, array_flip($validColumns));

        $property = \App\Models\Property::find($this->record);
        if ($property) {
            $translatableFields = ['name', 'description', 'meta_title', 'meta_description', 'intro_subtitle', 'intro_title', 'intro_text'];
            foreach ($translatableFields as $field) {
                if (isset($data[$field]) && is_array($data[$field])) {
                    $existing = $property->{$field} ?? [];
                    if (is_array($existing)) {
                        $data[$field] = array_merge($existing, $data[$field]);
                    }
                }
            }
            $property->update($data);
        }

        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageBrands::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageBrands::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
