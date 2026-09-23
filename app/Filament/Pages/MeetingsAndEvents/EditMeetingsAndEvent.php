<?php
namespace App\Filament\Pages\MeetingsAndEvents;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class EditMeetingsAndEvent extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-meetings-and-events/{record}/edit';
    

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $page = \App\Models\MeetingEventPage::findOrFail($this->record);
        $reverseTypeMapping = [
            'main_page' => 'Main Overview',
            'corporate' => 'Corporate Meetings',
            'weddings' => 'Weddings',
            'conference_room' => 'Conference Facilities',
            'events' => 'Banquet Halls',
            'outside_catering' => 'Outdoor Venues',
            'rfp' => 'Corporate Meetings',
        ];

        $desc = is_array($page->description) ? ($page->description['en'] ?? reset($page->description)) : $page->description;
        if (empty($desc) && !empty($page->details_content)) {
            $desc = is_array($page->details_content) ? ($page->details_content['en'] ?? reset($page->details_content)) : $page->details_content;
        }

        $title = is_array($page->title) ? ($page->title['en'] ?? reset($page->title)) : $page->title;
        $highlights = $page->highlights;
        if (is_array($highlights)) {
            $highlights = implode("\n\n", $highlights);
        }

        $this->form->fill([
            'title' => $title,
            'property_id' => $page->property_id,
            'event_type' => $reverseTypeMapping[$page->type] ?? 'Corporate Meetings',
            'slug' => $page->slug ?? \Illuminate\Support\Str::slug($title ?? ''),
            'status' => $page->status ?? ($page->is_active ? 'Published' : 'Draft'),
            'description' => $desc,
            'highlights' => $highlights,
            'rfp_url' => $page->rfp_url,
            'contact_phone' => is_array($page->contact_details) ? ($page->contact_details['phone'] ?? '') : '',
            'contact_email' => is_array($page->contact_details) ? ($page->contact_details['email'] ?? '') : '',
            'gallery' => $page->gallery ?? [],
            'meta_title' => $page->seoMetadata?->meta_title,
            'meta_description' => $page->seoMetadata?->meta_description,
            'meta_keywords' => $page->seoMetadata?->meta_keywords,
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('manageEventDetails')
                ->label('Manage Meetings & Events Details')
                ->icon('heroicon-o-document-text')
                ->url(fn () => \App\Filament\Pages\MeetingsAndEvents\MeetingsAndEventDetailsContent::getUrl(['record' => $this->record]))
        ];
    }

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageMeetingsAndEvents::getEventFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        \Illuminate\Support\Facades\Log::info('Meetings Save Data:', $data);
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

        $page = \App\Models\MeetingEventPage::findOrFail($this->record);
        $page->update([
            'title' => $data['title'] ?? 'Untitled',
            'type' => $mappedType,
            'description' => $data['description'] ?? '',
            'details_content' => $data['description'] ?? '',
            'highlights' => !empty($data['highlights']) ? $data['highlights'] : null,
            'capacity_details' => null,
            'area_sqm' => null,
            'area_sqft' => null,
            'ceiling_height' => null,
            'capacities' => null,
            'rfp_url' => $data['rfp_url'] ?? null,
            'contact_details' => $contactDetails,
            'gallery' => $gallery,
            'image' => $firstImage ?? $page->image,
            'slug' => $data['slug'] ?? \Illuminate\Support\Str::slug($data['title'] ?? 'Untitled'),
            'status' => $data['status'] ?? 'Published',
            'is_active' => ($data['status'] ?? 'Published') === 'Published',
            'property_id' => $data['property_id'] ?? 1,
        ]);
        
        if (isset($data['meta_title']) || isset($data['meta_description']) || isset($data['meta_keywords'])) {
            $page->seoMetadata()->updateOrCreate(
                ['seoable_id' => $page->id, 'seoable_type' => \App\Models\MeetingEventPage::class],
                [
                    'meta_title' => $data['meta_title'] ?? null,
                    'meta_description' => $data['meta_description'] ?? null,
                    'meta_keywords' => $data['meta_keywords'] ?? null,
                ]
            );
        }
        
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageMeetingsAndEvents::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageMeetingsAndEvents::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
