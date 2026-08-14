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

        $this->form->fill([
            'title' => $page->title,
            'hotel' => $page->property?->display_name,
            'event_type' => $reverseTypeMapping[$page->type] ?? 'Corporate Meetings',
            'slug' => \Illuminate\Support\Str::slug($page->title ?? ''),
            'status' => $page->is_active ? 'Published' : 'Draft',
            'highlight_description' => $page->description,
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
