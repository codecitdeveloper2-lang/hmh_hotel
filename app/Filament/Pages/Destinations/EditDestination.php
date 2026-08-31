<?php
namespace App\Filament\Pages\Destinations;

use App\Models\Destination;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Str;

class EditDestination extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-destinations/{record}/edit';

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;

        $destination = Destination::with(['cities', 'seoMetadata', 'media'])->findOrFail($record);

        // Build the form data array including all fields
        $formData = [
            'activeLocale'     => 'en',
            'id'               => $destination->id,
            'name'             => $destination->getTranslations('name'),
            'slug'             => $destination->slug,
            'country'          => $destination->country,
            'description'      => $destination->getTranslations('description'),
            'status'           => $destination->is_active ? 'Active' : 'Inactive',
            'display_order'    => $destination->sort_order,
            'meta_title'       => $destination->seoMetadata?->meta_title,
            'meta_description' => $destination->seoMetadata?->meta_description,
            'meta_keywords'    => $destination->seoMetadata?->meta_keywords,
            'map_embed_code'   => $destination->map_embed_code,
            'cities'           => $destination->cities
                ->sortBy('sort_order')
                ->map(fn($city): array => [
                    'id'          => $city->id,
                    'city_name'   => $city->getTranslations('name'),
                    'description' => $city->getTranslations('description'),
                    'city_image'  => $city->city_image,
                    'city_link'   => $city->city_link,
                    'layout_type' => $city->layout_type,
                    'sort_order'  => $city->sort_order,
                    'status'      => $city->is_active ? 'Active' : 'Inactive',
                    'hotel_labels'=> $city->hotel_labels ?? [],
                ])
                ->values()
                ->all(),
        ];

        $this->data = $formData;

        // Fill the form — Spatie media upload loads automatically via the record binding
        $this->form->fill($formData);
    }

    public function form($form)
    {
        $destination = $this->record ? Destination::find($this->record) : null;

        return $form
            ->schema(\App\Filament\Pages\ManageDestinations::getDestinationFormSchema())
            ->model(Destination::class)
            ->record($destination)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $destination = Destination::with(['cities', 'seoMetadata'])->findOrFail($this->record);

        $nameTranslations = array_replace(
            $destination->getTranslations('name'),
            is_array($data['name'] ?? null) ? $data['name'] : []
        );
        $descriptionTranslations = array_replace(
            $destination->getTranslations('description'),
            is_array($data['description'] ?? null) ? $data['description'] : []
        );

        // Update core destination fields
        $destination->update([
            'name'        => $nameTranslations,
            'description' => $descriptionTranslations,
            'slug'        => $data['slug'],
            'country'     => $data['country'] ?? null,
            'is_active'   => ($data['status'] ?? 'Active') === 'Active',
            'sort_order'  => $data['display_order'] ?? 0,
            'map_embed_code' => $data['map_embed_code'] ?? null,
        ]);

        // Sync cities
        $cities      = collect($data['cities'] ?? []);
        $keptCityIds = $cities->pluck('id')->filter()->map(fn($id) => (int) $id)->all();

        // Delete cities that were removed
        if ($keptCityIds) {
            $destination->cities()->whereNotIn('id', $keptCityIds)->delete();
        } else {
            $destination->cities()->delete();
        }

        // Update or create cities
        foreach ($cities as $cityData) {
            $cityName = $cityData['city_name'] ?? [];
            $city = ! empty($cityData['id'])
                ? $destination->cities()->whereKey($cityData['id'])->first()
                : null;

            $cityNameTranslations = array_replace(
                $city?->getTranslations('name') ?? [],
                is_array($cityName) ? $cityName : []
            );
            $cityDescriptionTranslations = array_replace(
                $city?->getTranslations('description') ?? [],
                is_array($cityData['description'] ?? null) ? $cityData['description'] : []
            );

            $payload = [
                'name'        => $cityNameTranslations,
                'description' => $cityDescriptionTranslations,
                'slug'        => Str::slug($cityNameTranslations['en'] ?? $cityNameTranslations['ar'] ?? 'city'),
                'city_image'  => (!empty($cityData['city_image']) && !str_starts_with((string)$cityData['city_image'], 'livewire-file:')) ? $cityData['city_image'] : ($city?->city_image ?? null),
                'city_link'   => $cityData['city_link'] ?? null,
                'layout_type' => $cityData['layout_type'] ?? null,
                'sort_order'  => $cityData['sort_order'] ?? 0,
                'is_active'   => ($cityData['status'] ?? 'Active') === 'Active',
                'hotel_labels'=> $cityData['hotel_labels'] ?? [],
            ];

            if (! empty($cityData['id'])) {
                $city?->update($payload);
            } else {
                $destination->cities()->create($payload);
            }
        }

        // Update or create SEO metadata
        if (!empty($data['meta_title']) || !empty($data['meta_description']) || !empty($data['meta_keywords'])) {
            $destination->seoMetadata()->updateOrCreate([], [
                'meta_title'       => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords'    => $data['meta_keywords'] ?? null,
            ]);
        }

        // Save Spatie media relationships (banner images)
        $this->form->model($destination);
        $this->form->saveRelationships();

        \Filament\Notifications\Notification::make()
            ->title('Destination updated successfully!')
            ->success()
            ->send();

        $this->redirect(\App\Filament\Pages\ManageDestinations::getUrl());
    }

    public function getBackUrl(): string
    {
        return \App\Filament\Pages\ManageDestinations::getUrl();
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
