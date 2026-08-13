<?php
namespace App\Filament\Pages\Destinations;

use App\Models\Destination;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class CreateDestination extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-destinations/create';
    

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form($form)
    {
        return $form
            ->schema(\App\Filament\Pages\ManageDestinations::getDestinationFormSchema())
            ->model(Destination::class)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save the basic Destination fields
        $destination = Destination::create([
            'name' => $data['name'], // Data is now already ['en' => '...', 'ar' => '...']
            'description' => $data['description'] ?? null,
            'slug' => $data['slug'],
            'country' => $data['country'] ?? null,
            'is_active' => ($data['status'] ?? 'Active') === 'Active',
            'sort_order' => $data['display_order'] ?? 0,
            'map_embed_code' => $data['map_embed_code'] ?? null,
        ]);

        if (isset($data['cities']) && is_array($data['cities'])) {
            foreach ($data['cities'] as $cityData) {
                $destination->cities()->create([
                    'name' => $cityData['city_name'],
                    'description' => $cityData['description'] ?? null,
                    'slug' => \Illuminate\Support\Str::slug($cityData['city_name']['en'] ?? $cityData['city_name']['ar'] ?? 'city'),
                    'city_image' => (is_string($cityData['city_image'] ?? null) && str_starts_with($cityData['city_image'], 'livewire-file:')) ? null : ($cityData['city_image'] ?? null),
                    'city_link' => $cityData['city_link'] ?? null,
                    'layout_type' => $cityData['layout_type'] ?? null,
                    'sort_order' => $cityData['sort_order'] ?? 0,
                    'is_active' => ($cityData['status'] ?? 'Active') === 'Active',
                    'hotel_labels' => $cityData['hotel_labels'] ?? [],
                ]);
            }
        }

        $this->form->model($destination);
        $this->form->saveRelationships();

        if (!empty($data['meta_title']) || !empty($data['meta_description']) || !empty($data['meta_keywords'])) {
            $destination->seoMetadata()->create([
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
            ]);
        }

        \Filament\Notifications\Notification::make()->title('Destination created successfully in Database!')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageDestinations::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageDestinations::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
