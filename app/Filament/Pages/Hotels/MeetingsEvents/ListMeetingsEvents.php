<?php

namespace App\Filament\Pages\Hotels\MeetingsEvents;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
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

class ListMeetingsEvents extends Page implements HasForms
{
    use InteractsWithForms, HasHotelTabs;

    protected string $view = 'filament.pages.manage-meetings-events';

    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;
    protected static ?string $slug = 'manage-hotels/{record}/meetings-events';
    protected static bool $shouldRegisterNavigation = false;

    public int $perPage = 10;
    public int $currentPage = 1;

    public function mount($record): void
    {
        $this->mountHasHotelTabs($record);
    }

    public function updatedPerPage(): void { $this->currentPage = 1; }

    public function nextPage(int $lastPage): void
    {
        if ($this->currentPage < $lastPage) $this->currentPage++;
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) $this->currentPage--;
    }

    public function gotoPage(int $page): void
    {
        $this->currentPage = $page;
    }

    protected function getViewData(): array
    {
        // Exclude main_page from event spaces list table so table contains event venues only
        $query = MeetingEventPage::where('property_id', $this->record)
            ->where('type', '!=', 'main_page')
            ->orderBy('id');

        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));

        $events = $query->forPage($currentPage, $this->perPage)->get()->map(function ($e) {
            $img = $e->image;
            $gallery = is_array($e->gallery) ? $e->gallery : (is_string($e->gallery) ? json_decode($e->gallery, true) : []);
            if (!$img && !empty($gallery)) {
                $img = is_array($gallery[0]) ? ($gallery[0]['image'] ?? $gallery[0]['src'] ?? null) : $gallery[0];
            }
            $imageUrl = $img ? (str_starts_with($img, 'http') ? $img : url('uploads/' . ltrim($img, '/'))) : null;
            $galleryCount = is_array($gallery) ? count($gallery) : 0;

            return [
                'id' => $e->id,
                'title' => is_array($e->title) ? ($e->title['en'] ?? '') : $e->title,
                'subtitle' => is_array($e->subtitle) ? ($e->subtitle['en'] ?? '') : $e->subtitle,
                'status' => $e->is_active ? 'Active' : 'Inactive',
                'image_url' => $imageUrl,
                'gallery_count' => $galleryCount,
            ];
        });

        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'events', 'from', 'to');
    }

    public function getTitle(): string | Htmlable
    {
        $property = Property::find($this->record);
        return 'Meetings & Events - ' . ($property?->display_name ?? '');
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Meetings & Events';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage meeting rooms, corporate venues, wedding ballrooms, and main landing page content for this hotel.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('editLandingPageContent')
                ->label('Edit Main Page Content & Banners')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->modalWidth('7xl')
                ->form([
                    Grid::make(3)->schema([
                        Grid::make(1)->schema([
                            Section::make('Main Landing Page Header')
                                ->schema([
                                    TextInput::make('subtitle.en')
                                        ->label('Page Subtitle (e.g. MEETINGS & EVENTS)')
                                        ->required(),
                                    TextInput::make('title.en')
                                        ->label('Page Title (e.g. Discover our meeting rooms in Ajman)')
                                        ->required(),
                                    \App\Filament\Forms\Components\JoditEditor::make('description.en')
                                        ->label('Intro Description Text')
                                        ->required(),
                                    TextInput::make('rfp_url')
                                        ->label('Request for Proposal (RFP) Link')
                                        ->url(),
                                ]),

                            Section::make('Hero Banner Slides')
                                ->schema([
                                    \Filament\Forms\Components\Repeater::make('banner_slides')
                                        ->label('Banner Header Slides')
                                        ->schema([
                                            FileUpload::make('image')
                                                ->label('Background Image')
                                                ->disk('uploads')
                                                ->directory('')
                                                ->image()
                                                ->required(),
                                            TextInput::make('title')
                                                ->label('Slide Title'),
                                            TextInput::make('button_text')
                                                ->label('Button Text'),
                                            TextInput::make('button_link')
                                                ->label('Button Link')
                                                ->url(),
                                        ])
                                        ->collapsible()
                                        ->cloneable()
                                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Banner Slide'),
                                ]),
                        ])->columnSpan(2),

                        Grid::make(1)->schema([
                            Section::make('Landing Page SEO')
                                ->schema([
                                    TextInput::make('meta_title')
                                        ->label('Meta Title'),
                                    Textarea::make('meta_description')
                                        ->label('Meta Description')
                                        ->rows(4),
                                ]),
                        ])->columnSpan(1),
                    ])
                ])
                ->fillForm(function () {
                    $mainPage = MeetingEventPage::where('property_id', $this->record)
                        ->where('type', 'main_page')
                        ->first();

                    if (!$mainPage) return [];

                    return [
                        'subtitle' => $mainPage->getTranslations('subtitle'),
                        'title' => $mainPage->getTranslations('title'),
                        'description' => $mainPage->getTranslations('description'),
                        'rfp_url' => $mainPage->rfp_url,
                        'banner_slides' => $mainPage->banner_slides ?? [],
                        'meta_title' => $mainPage->seoMetadata?->meta_title,
                        'meta_description' => $mainPage->seoMetadata?->meta_description,
                    ];
                })
                ->action(function (array $data) {
                    $mainPage = MeetingEventPage::updateOrCreate(
                        [
                            'property_id' => $this->record,
                            'type' => 'main_page',
                        ],
                        [
                            'subtitle' => is_array($data['subtitle']) ? $data['subtitle'] : ['en' => $data['subtitle']],
                            'title' => is_array($data['title']) ? $data['title'] : ['en' => $data['title']],
                            'description' => is_array($data['description']) ? $data['description'] : ['en' => $data['description']],
                            'rfp_url' => $data['rfp_url'] ?? null,
                            'banner_slides' => $data['banner_slides'] ?? [],
                            'slug' => 'meetings-events',
                            'is_active' => true,
                            'status' => 'published',
                        ]
                    );

                    if (!empty($data['meta_title']) || !empty($data['meta_description'])) {
                        $mainPage->seoMetadata()->updateOrCreate([], [
                            'meta_title' => $data['meta_title'] ?? null,
                            'meta_description' => $data['meta_description'] ?? null,
                        ]);
                    }

                    Notification::make()
                        ->title('Landing Page Content Saved')
                        ->body('Main Meetings & Events landing page content has been updated.')
                        ->success()
                        ->send();
                }),

            Action::make('addEvent')
                ->label('Add Event Space')
                ->icon('heroicon-o-plus')
                ->modalHeading('Add Event Space')
                ->modalDescription('Create a new venue or event space for this hotel.')
                ->modalIcon('heroicon-o-plus-circle')
                ->modalSubmitActionLabel('Create Event Space')
                ->modalCancelActionLabel('Cancel')
                ->modalWidth('4xl')
                ->form($this->getEventFormSchema())
                ->action(function (array $data) {
                    $title = is_array($data['title']) ? ($data['title']['en'] ?? 'event') : $data['title'];
                    $gallery = is_array($data['gallery'] ?? null) ? array_values($data['gallery']) : [];
                    $coverImage = !empty($data['image']) ? $data['image'] : ($gallery[0] ?? null);

                    MeetingEventPage::create([
                        'property_id' => $this->record,
                        'type' => 'events',
                        'slug' => Str::slug($title),
                        'title' => ['en' => $title],
                        'subtitle' => ['en' => $data['subtitle'] ?? ''],
                        'description' => ['en' => $data['description'] ?? ''],
                        'rfp_url' => $data['rfp_url'] ?? null,
                        'image' => $coverImage,
                        'gallery' => $gallery,
                        'is_active' => (bool) ($data['is_active'] ?? true),
                    ]);

                    Notification::make()
                        ->title('Event Created')
                        ->body('New event space has been added.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function editEventAction(): Action
    {
        return Action::make('editEvent')
            ->modalHeading('Edit Basic Info')
            ->modalDescription('Update title, overview description, media gallery, and booking link for this venue.')
            ->modalIcon('heroicon-o-pencil-square')
            ->modalSubmitActionLabel('Save Changes')
            ->modalCancelActionLabel('Cancel')
            ->modalWidth('4xl')
            ->form($this->getEventFormSchema())
            ->fillForm(function (array $arguments) {
                $e = MeetingEventPage::find($arguments['id']);
                if (!$e) return [];
                return [
                    'title' => is_array($e->title) ? ($e->title['en'] ?? '') : $e->title,
                    'subtitle' => is_array($e->subtitle) ? ($e->subtitle['en'] ?? '') : $e->subtitle,
                    'description' => is_array($e->description) ? ($e->description['en'] ?? '') : $e->description,
                    'rfp_url' => $e->rfp_url,
                    'image' => $e->image,
                    'gallery' => $e->gallery ?? [],
                    'is_active' => (bool) ($e->is_active ?? true),
                ];
            })
            ->action(function (array $data, array $arguments) {
                $e = MeetingEventPage::find($arguments['id']);
                if ($e) {
                    $gallery = is_array($data['gallery'] ?? null) ? array_values($data['gallery']) : [];
                    $coverImage = !empty($data['image']) ? $data['image'] : ($gallery[0] ?? null);

                    $e->update([
                        'title' => ['en' => $data['title']],
                        'subtitle' => ['en' => $data['subtitle'] ?? ''],
                        'description' => ['en' => $data['description'] ?? ''],
                        'rfp_url' => $data['rfp_url'] ?? null,
                        'image' => $coverImage,
                        'gallery' => $gallery,
                        'is_active' => (bool) ($data['is_active'] ?? true),
                    ]);
                    Notification::make()
                        ->title('Event Updated')
                        ->body('Event space basic information and gallery photos have been updated successfully.')
                        ->success()
                        ->send();
                }
            });
    }

    public function deleteEventAction(): Action
    {
        return Action::make('deleteEvent')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                MeetingEventPage::find($arguments['id'])?->delete();
                Notification::make()
                    ->title('Event Deleted')
                    ->success()
                    ->send();
            });
    }

    public static function getEventFormSchema(): array
    {
        return [
            Section::make('General Information')
                ->description('Specify the venue name, category, and an engaging overview description.')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('title')
                            ->label('Event / Venue Title')
                            ->placeholder('e.g. Corporate Meetings, Banquet Halls, Weddings')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('subtitle')
                            ->label('Subtitle / Category')
                            ->placeholder('e.g. Business Conferences, Private Receptions')
                            ->maxLength(255),
                    ]),
                    RichEditor::make('description')
                        ->label('Short Overview')
                        ->placeholder('Provide an engaging description of this event space, its ambiance, and facilities...')
                        ->toolbarButtons([
                            'bold',
                            'italic',
                            'underline',
                            'strike',
                            'bulletList',
                            'orderedList',
                            'link',
                            'undo',
                            'redo',
                        ])
                        ->columnSpanFull(),
                ]),

            Section::make('Media & Gallery')
                ->description('Upload the venue cover photo, photo gallery for the slider, and configure the Request for Proposal (RFP) booking link.')
                ->schema([
                    Grid::make(2)->schema([
                        FileUpload::make('image')
                            ->label('Cover Image (Featured / Thumbnail)')
                            ->disk('uploads')
                            ->directory('')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9')
                            ->helperText('Main cover photo for cards and previews.')
                            ->columnSpan(1),
                        Grid::make(1)->schema([
                            TextInput::make('rfp_url')
                                ->label('RFP / Request Link')
                                ->placeholder('/meetings-events/request-for-proposal or https://...')
                                ->helperText('Target link for the proposal request button. Supports relative paths or external URLs.'),
                            Toggle::make('is_active')
                                ->label('Active Status')
                                ->helperText('Toggle whether this event venue is visible on the website.')
                                ->default(true),
                        ])->columnSpan(1),
                    ]),
                    FileUpload::make('gallery')
                        ->label('Slider & Gallery Images')
                        ->disk('uploads')
                        ->directory('')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->helperText('Upload all photos displayed in the venue image slider & thumbnail strip on the website.')
                        ->columnSpanFull(),
                ]),
        ];
    }
}
