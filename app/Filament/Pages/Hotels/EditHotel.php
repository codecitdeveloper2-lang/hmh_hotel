<?php
namespace App\Filament\Pages\Hotels;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;

class EditHotel extends Page implements HasForms
{
    use HasHotelTabs;
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/edit';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public string $activeLocale = 'en';
    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $property = \App\Models\Property::findOrFail($record);
        $data = $property->toArray();

        // No need to extract English values, we use data.name.en and data.name.ar now

        $this->form->fill($data);
    }

    public function getTitle(): string | \Illuminate\Contracts\Support\Htmlable
    {
        $property = \App\Models\Property::find($this->record);
        return $property?->display_name ?? 'Edit Hotel';
    }



    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageHotels::getHotelFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Capture all localized inputs from Livewire state, not just the active (visible) locale
        $translatableFields = ['name', 'description', 'meta_title', 'meta_description', 'intro_subtitle', 'intro_title', 'intro_text'];
        foreach ($translatableFields as $field) {
            if (isset($this->data[$field]) && is_array($this->data[$field])) {
                $data[$field] = array_merge($data[$field] ?? [], $this->data[$field]);
            }
        }

        $validColumns = [
            'parent_id', 'name', 'description', 'slug', 'star_rating',
            'address', 'city', 'country', 'latitude', 'longitude',
            'phone', 'email', 'travelclick_hotel_id', 'attractions_page_slug',
            'status', 'check_in_time', 'check_out_time',
            'meta_title', 'meta_description', 'is_active', 'sort_order',
            'is_featured', 'intro_subtitle', 'intro_title', 'banner_slides',
            'cover_image', 'banner_images', 'logo', 'website',
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
        $this->redirect(\App\Filament\Pages\ManageHotels::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageHotels::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
