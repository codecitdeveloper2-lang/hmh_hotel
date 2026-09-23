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
