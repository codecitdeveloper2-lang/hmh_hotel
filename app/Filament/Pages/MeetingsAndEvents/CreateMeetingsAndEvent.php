<?php
namespace App\Filament\Pages\MeetingsAndEvents;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class CreateMeetingsAndEvent extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-meetings-and-events/create';
    

    public ?array $data = [];

    public function mount(): void
    {
        $defaultHotelId = \App\Models\Property::where('type', 'hotel')->where('slug', 'opera-grand-hotel')->value('id')
            ?? \App\Models\Property::where('type', 'hotel')->value('id');

        $this->form->fill([
            'property_id' => $defaultHotelId,
            'status' => 'Published',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('manageEventDetails')
                ->label('Manage Meetings & Events Details')
                ->icon('heroicon-o-document-text')
                ->disabled()
                ->tooltip('Please create the event first to manage its details.')
        ];
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageMeetingsAndEvents::getEventFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $typeMapping = [
            'Main Overview' => 'main_page',
            'Corporate Meetings' => 'corporate',
            'Weddings' => 'weddings',
            'Conference Facilities' => 'conference_room',
            'Banquet Halls' => 'events',
            'Private Events' => 'events',
            'Outdoor Venues' => 'outside_catering',
        ];
        $mappedType = $typeMapping[$data['event_type'] ?? ''] ?? 'corporate';

        $contactDetails = [
            'phone' => $data['contact_phone'] ?? null,
            'email' => $data['contact_email'] ?? null,
        ];

        $gallery = is_array($data['gallery'] ?? null) ? array_values($data['gallery']) : [];
        $firstImage = count($gallery) > 0 ? $gallery[0] : null;

        $page = \App\Models\MeetingEventPage::create([
            'title' => $data['title'] ?? 'Untitled',
            'type' => $mappedType,
            'description' => $data['description'] ?? '',
            'details_content' => $data['description'] ?? '',
            'rfp_url' => $data['rfp_url'] ?? null,
            'contact_details' => $contactDetails,
            'highlights' => !empty($data['highlights']) ? $data['highlights'] : null,
            'capacity_details' => null,
            'area_sqm' => null,
            'area_sqft' => null,
            'ceiling_height' => null,
            'capacities' => null,
            'gallery' => $gallery,
            'image' => $firstImage,
            'slug' => $data['slug'] ?? \Illuminate\Support\Str::slug($data['title'] ?? 'Untitled'),
            'status' => $data['status'] ?? 'Published',
            'is_active' => ($data['status'] ?? 'Published') === 'Published',
            'property_id' => $data['property_id'] ?? 1,
        ]);
        
        if (isset($data['meta_title']) || isset($data['meta_description']) || isset($data['meta_keywords'])) {
            $page->seoMetadata()->create([
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
            ]);
        }

        Notification::make()->title('Created successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageMeetingsAndEvents::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageMeetingsAndEvents::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
