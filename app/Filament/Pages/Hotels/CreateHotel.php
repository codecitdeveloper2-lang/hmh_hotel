<?php
namespace App\Filament\Pages\Hotels;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateHotel extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/create';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public string $activeLocale = 'en';
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }



    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageHotels::getHotelFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['type'] = 'hotel';

        // Capture all localized inputs from Livewire state, not just the active (visible) locale
        $translatableFields = ['name', 'description', 'meta_title', 'meta_description', 'intro_subtitle', 'intro_title', 'intro_text'];
        foreach ($translatableFields as $field) {
            if (isset($this->data[$field]) && is_array($this->data[$field])) {
                $data[$field] = array_merge($data[$field] ?? [], $this->data[$field]);
            }
        }

        $validColumns = [
            'parent_id', 'type', 'name', 'description', 'slug', 'star_rating',
            'address', 'city', 'country', 'latitude', 'longitude',
            'phone', 'email', 'travelclick_hotel_id', 'attractions_page_slug',
            'status', 'check_in_time', 'check_out_time',
            'meta_title', 'meta_description', 'is_active', 'sort_order',
            'is_featured', 'intro_subtitle', 'intro_title', 'banner_slides',
            'cover_image', 'website',
        ];
        $data = array_intersect_key($data, array_flip($validColumns));

        \App\Models\Property::create($data);
        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageHotels::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageHotels::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
