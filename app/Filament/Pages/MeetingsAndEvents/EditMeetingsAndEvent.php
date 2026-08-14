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
        
        $this->form->fill([
            'title' => $page->title,
            'hotel' => $page->property?->display_name,
            'event_type' => $reverseTypeMapping[$page->type] ?? 'Corporate Meetings',
            'slug' => \Illuminate\Support\Str::slug($page->title ?? ''),
            'status' => $page->is_active ? 'Published' : 'Draft',
            'highlight_description' => $page->description,
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
        $property = \App\Models\Property::where('name', 'like', '%' . ($data['hotel'] ?? '') . '%')->first();
        
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
            'is_active' => ($data['status'] ?? 'Published') === 'Published',
            'property_id' => $property ? $property->id : 1,
        ]);
        
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\ManageMeetingsAndEvents::getUrl());
    }

    public function getBackUrl(): string { return \App\Filament\Pages\ManageMeetingsAndEvents::getUrl(); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
