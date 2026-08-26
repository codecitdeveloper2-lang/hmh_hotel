<?php
namespace App\Filament\Pages\Brands;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateBrand extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-brands/create';
    

    public string $activeLocale = 'en';
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageBrands::getBrandFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['type'] = 'brand';

        $validColumns = [
            'type', 'name', 'description', 'slug', 'star_rating',
            'address', 'city', 'country', 'latitude', 'longitude',
            'phone', 'email', 'travelclick_hotel_id', 'attractions_page_slug',
            'status', 'check_in_time', 'check_out_time',
            'meta_title', 'meta_description', 'is_active', 'sort_order',
            'tagline', 'google_location', 'location_title', 'contact_button_text',
            'contact_button_url', 'star_segment', 'logo', 'intro_text',
            'banner_title', 'banner_images', 'intro_subtitle', 'intro_title',
            'brand_content',
        ];
        $data = array_intersect_key($data, array_flip($validColumns));

        \App\Models\Property::create($data);

        \Filament\Notifications\Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageBrands::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageBrands::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
