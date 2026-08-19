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
            'corporate' => 'Corporate Meetings',
            'weddings' => 'Weddings',
            'conference_room' => 'Conference Facilities',
            'events' => 'Banquet Halls',
            'outside_catering' => 'Outdoor Venues',
            'rfp' => 'Corporate Meetings',
        ];
        
        $rekeyRepeater = function ($array) {
            if (!is_array($array)) return [];
            $result = [];
            foreach ($array as $item) {
                $result[(string)\Illuminate\Support\Str::uuid()] = $item;
            }
            return $result;
        };

        $this->form->fill([
            'title' => $page->title,
            'property_id' => $page->property_id,
            'event_type' => $reverseTypeMapping[$page->type] ?? 'Corporate Meetings',
            'slug' => $page->slug ?? \Illuminate\Support\Str::slug($page->title ?? ''),
            'status' => $page->status ?? ($page->is_active ? 'Published' : 'Draft'),
            'highlight_subtitle' => $page->subtitle,
            'highlight_title' => $page->title ?? '', // not strictly saved separately, fallback to title
            'highlight_description' => $page->description,
            'rfp_url' => $page->rfp_url,
            'event_cards' => $rekeyRepeater($page->event_cards),
            'banner_slides' => $rekeyRepeater($page->banner_slides),
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
            'Corporate Meetings' => 'corporate',
            'Weddings' => 'weddings',
            'Conference Facilities' => 'conference_room',
            'Banquet Halls' => 'events',
            'Private Events' => 'events',
            'Outdoor Venues' => 'outside_catering',
        ];
        $mappedType = $typeMapping[$data['event_type'] ?? ''] ?? 'corporate';
        
        $page = \App\Models\MeetingEventPage::findOrFail($this->record);
        $page->update([
            'title' => $data['title'] ?? 'Untitled',
            'type' => $mappedType,
            'description' => $data['highlight_description'] ?? '',
            'subtitle' => $data['highlight_subtitle'] ?? null,
            'rfp_url' => $data['rfp_url'] ?? null,
            'event_cards' => $data['event_cards'] ?? [],
            'banner_slides' => $data['banner_slides'] ?? [],
            'gallery' => $data['gallery'] ?? [],
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
