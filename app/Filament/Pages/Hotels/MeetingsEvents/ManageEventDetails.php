<?php

namespace App\Filament\Pages\Hotels\MeetingsEvents;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use App\Models\MeetingEventPage;
use App\Models\Property;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class ManageEventDetails extends Page implements HasForms
{
    use InteractsWithForms, HasHotelTabs;

    protected string $view = 'filament.pages.generic-create-edit';

    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;
    protected static ?string $slug = 'manage-hotels/{record}/meetings-events/{event_id}/details';
    protected static bool $shouldRegisterNavigation = false;

    public $event_id;
    public ?array $data = [];

    public function mount($record, $event_id): void
    {
        $this->mountHasHotelTabs($record);
        $this->event_id = $event_id;

        $event = MeetingEventPage::where('property_id', $record)->where('id', $event_id)->firstOrFail();

        $contact = $event->contact_details ?? [];
        $formData = [
            'title' => $event->getTranslations('title'),
            'subtitle' => $event->getTranslations('subtitle'),
            'slug' => $event->slug,
            'description' => $event->getTranslations('description'),
            'details_content' => $event->getTranslations('details_content'),
            'rfp_url' => $event->rfp_url,
            'is_active' => $event->is_active,
            'image' => $event->image,
            'gallery' => $event->gallery ?? [],
            'contact_email' => $contact['email'] ?? '',
            'contact_phone' => $contact['phone'] ?? '',
            'meta_title' => $event->seoMetadata?->meta_title,
            'meta_description' => $event->seoMetadata?->meta_description,
        ];

        $this->form->fill($formData);
    }

    public function getTitle(): string | Htmlable
    {
        $event = MeetingEventPage::find($this->event_id);
        $name = $event ? (is_array($event->title) ? ($event->title['en'] ?? '') : $event->title) : '';
        return 'Event Details - ' . $name;
    }

    public function getHeading(): string | Htmlable | null
    {
        $event = MeetingEventPage::find($this->event_id);
        $name = $event ? (is_array($event->title) ? ($event->title['en'] ?? '') : $event->title) : 'Event';
        return "Event Details: {$name}";
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Configure venue dimensions, seating layout capacities, highlights, rich description, contact details, gallery, and SEO metadata.';
    }

    public function form($form)
    {
        return $form
            ->schema([
                Grid::make(3)->schema([
                    Grid::make(1)->schema([
                        Section::make('Event & Venue Overview')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('subtitle.en')
                                        ->label('Subtitle / Category (e.g. Weddings, Corporate Meetings)'),
                                    TextInput::make('slug')
                                        ->label('Slug')
                                        ->required(),
                                ]),
                                TextInput::make('title.en')
                                    ->label('Venue / Event Title')
                                    ->required(),
                                Textarea::make('description.en')
                                    ->label('Short Overview')
                                    ->rows(3),
                                \App\Filament\Forms\Components\JoditEditor::make('details_content.en')
                                    ->label('Detailed Page Content')
                                    ->required(),
                                TextInput::make('rfp_url')
                                    ->label('RFP / Request Proposal Link')
                                    ->url(),
                                Toggle::make('is_active')
                                    ->label('Active Status')
                                    ->default(true),
                            ]),



                        Section::make('Contact Details')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('contact_phone')
                                        ->label('Contact Phone')
                                        ->tel(),
                                    TextInput::make('contact_email')
                                        ->label('Contact Email')
                                        ->email(),
                                ]),
                            ]),
                    ])->columnSpan(2),

                    Grid::make(1)->schema([
                        Section::make('Media & Gallery')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Hero / Cover Image')
                                    ->disk('uploads')
                                    ->directory('')
                                    ->image(),
                                FileUpload::make('gallery')
                                    ->label('Venue Gallery Images')
                                    ->disk('uploads')
                                    ->directory('')
                                    ->multiple()
                                    ->image(),
                            ]),

                        Section::make('SEO Metadata')
                            ->schema([
                                TextInput::make('meta_title')
                                    ->label('Meta Title'),
                                Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(3),
                            ]),
                    ])->columnSpan(1),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $event = MeetingEventPage::where('property_id', $this->record)->where('id', $this->event_id)->firstOrFail();

        $event->update([
            'title' => is_array($data['title'] ?? null) ? $data['title'] : ['en' => $data['title'] ?? ''],
            'subtitle' => is_array($data['subtitle'] ?? null) ? $data['subtitle'] : ['en' => $data['subtitle'] ?? ''],
            'slug' => $data['slug'] ?? Str::slug(is_array($data['title']) ? ($data['title']['en'] ?? 'event') : $data['title']),
            'description' => is_array($data['description'] ?? null) ? $data['description'] : ['en' => $data['description'] ?? ''],
            'details_content' => is_array($data['details_content'] ?? null) ? $data['details_content'] : ['en' => $data['details_content'] ?? ''],
            'rfp_url' => $data['rfp_url'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'image' => $data['image'] ?? null,
            'gallery' => $data['gallery'] ?? [],
            'contact_details' => [
                'email' => $data['contact_email'] ?? '',
                'phone' => $data['contact_phone'] ?? '',
            ],
        ]);

        if (!empty($data['meta_title']) || !empty($data['meta_description'])) {
            $event->seoMetadata()->updateOrCreate([], [
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
            ]);
        }

        Notification::make()
            ->title('Event Details saved successfully!')
            ->success()
            ->send();
    }

    public function getBackUrl(): string
    {
        return url('/hotel-management/manage-hotels/' . $this->record . '/meetings-events');
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
