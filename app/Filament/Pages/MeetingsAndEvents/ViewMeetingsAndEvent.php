<?php
namespace App\Filament\Pages\MeetingsAndEvents;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ViewMeetingsAndEvent extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-meetings-and-events/{record}/view';
    

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

    public function form($form)
    {
        return $form->schema(\App\Filament\Pages\ManageMeetingsAndEvents::getEventFormSchema())->disabled()->statePath('data');
    }
    
    public function getBackUrl(): string { return \App\Filament\Pages\ManageMeetingsAndEvents::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
