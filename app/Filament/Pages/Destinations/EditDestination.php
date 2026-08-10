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
        $this->data = \App\Filament\Pages\ManageDestinations::getDestinationFormData($this->record);
        abort_if(empty($this->data), 404);

        $this->form->fill($this->data);
    }

    public function form($form)
    {
        return $form
            ->schema(\App\Filament\Pages\ManageDestinations::getDestinationFormSchema())
            ->model(Destination::class)
            ->record($this->record ? Destination::find($this->record) : null)
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $destination = Destination::with(['cities', 'seoMetadata'])->findOrFail($this->record);

        $destination->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'slug' => $data['slug'],
            'country' => $data['country'] ?? null,
            'is_active' => ($data['status'] ?? 'Active') === 'Active',
            'sort_order' => $data['display_order'] ?? 0,
        ]);

        $cities = collect($data['cities'] ?? []);
        $keptCityIds = $cities->pluck('id')->filter()->map(fn($id) => (int) $id)->all();

        if ($keptCityIds) {
            $destination->cities()->whereNotIn('id', $keptCityIds)->delete();
        } else {
            $destination->cities()->delete();
        }

        foreach ($cities as $cityData) {
            $payload = [
                'name' => $cityData['city_name'],
                'description' => $cityData['description'] ?? null,
                'slug' => Str::slug($cityData['city_name']['en'] ?? ''),
                'city_image' => $cityData['city_image'] ?? null,
                'city_link' => $cityData['city_link'] ?? null,
                'layout_type' => $cityData['layout_type'] ?? null,
                'sort_order' => $cityData['sort_order'] ?? 0,
                'is_active' => ($cityData['status'] ?? 'Active') === 'Active',
                'hotel_labels' => $cityData['hotel_labels'] ?? [],
            ];

            if (! empty($cityData['id'])) {
                $destination->cities()->whereKey($cityData['id'])->first()?->update($payload);
            } else {
                $destination->cities()->create($payload);
            }
        }

        if (!empty($data['meta_title']) || !empty($data['meta_description']) || !empty($data['meta_keywords'])) {
            $destination->seoMetadata()->updateOrCreate([], [
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
            ]);
        }

        $this->form->model($destination);
        $this->form->saveRelationships();

        \Filament\Notifications\Notification::make()->title('Destination updated successfully!')->success()->send();
        $this->redirect(ManageDestinations::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageDestinations::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
