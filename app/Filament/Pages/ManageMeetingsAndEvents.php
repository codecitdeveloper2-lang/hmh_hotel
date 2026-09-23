<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class ManageMeetingsAndEvents extends Page
{
    protected string $view = 'filament.pages.manage-meetings-and-events';

    public $searchQuery = '';
    public $filterHotel = '';
    public $filterEventType = '';
    public $filterStatus = '';


    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void
    {
        $this->currentPage = 1;
    }
    public function updatedFilterHotel(): void
    {
        $this->currentPage = 1;
    }
    public function updatedFilterEventType(): void
    {
        $this->currentPage = 1;
    }
    public function updatedFilterStatus(): void
    {
        $this->currentPage = 1;
    }
    public function updatedPerPage(): void
    {
        $this->currentPage = 1;
    }

    public function nextPage(int $lastPage): void
    {
        if ($this->currentPage < $lastPage)
            $this->currentPage++;
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1)
            $this->currentPage--;
    }

    public function gotoPage(int $page): void
    {
        $this->currentPage = $page;
    }

    protected function getViewData(): array
    {
        $query = \App\Models\MeetingEventPage::query()->with('property');

        if ($this->searchQuery) {
            $query->where('title', 'like', "%{$this->searchQuery}%");
        }

        if ($this->filterHotel) {
            $query->where('property_id', $this->filterHotel);
        }

        if ($this->filterEventType) {
            $typeMap = [
                'Main Overview' => 'main_page',
                'Corporate Meetings' => 'corporate',
                'Weddings' => 'weddings',
                'Conference Facilities' => 'conference_room',
                'Banquet Halls' => 'events',
                'Private Events' => 'events',
                'Outdoor Venues' => 'outside_catering',
            ];
            $dbType = $typeMap[$this->filterEventType] ?? $this->filterEventType;
            $query->where('type', $dbType);
        }

        if ($this->filterStatus) {
            if ($this->filterStatus === 'Published') {
                $query->where('is_active', 1);
            } elseif ($this->filterStatus === 'Draft') {
                $query->where('is_active', 0);
            }
        }

        $totalItems = $query->count();
        $lastPage = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));

        $eventPages = $query->skip(($currentPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($page) {
                $img = $page->image;
                if (!$img && !empty($page->gallery) && is_array($page->gallery) && count($page->gallery) > 0) {
                    $img = array_values($page->gallery)[0];
                }
                if (!$img && !empty($page->banner_slides) && is_array($page->banner_slides) && count($page->banner_slides) > 0) {
                    $firstSlide = array_values($page->banner_slides)[0];
                    $img = $firstSlide['banner_image'] ?? null;
                }
                if (!$img && !empty($page->event_cards) && is_array($page->event_cards) && count($page->event_cards) > 0) {
                    $firstCard = array_values($page->event_cards)[0];
                    $img = $firstCard['image'] ?? null;
                }
                $featuredImage = is_array($img) ? (count($img) > 0 ? array_values($img)[0] : null) : $img;

                $typeLabels = [
                    'main_page' => 'Main Overview',
                    'corporate' => 'Corporate Meetings',
                    'weddings' => 'Weddings',
                    'conference_room' => 'Conference Facilities',
                    'events' => 'Banquet Halls',
                    'outside_catering' => 'Outdoor Venues',
                ];
                $eventType = $typeLabels[$page->type] ?? ucfirst(str_replace('_', ' ', $page->type));
                
                return [
                    'id' => $page->id,
                    'title' => is_array($page->title) ? ($page->title['en'] ?? reset($page->title)) : $page->title,
                    'hotel' => $page->property?->display_name ?? 'Unknown',
                    'event_type' => $eventType,
                    'status' => $page->is_active ? 'Published' : 'Draft',
                    'last_updated' => $page->updated_at?->format('Y-m-d') ?? '',
                    'venue_capacity' => is_array($page->capacity_details) ? ($page->capacity_details['en'] ?? reset($page->capacity_details)) : ($page->capacity_details ?? 'N/A'),
                    'cta_text' => 'Book Now',
                    'featured_image' => $featuredImage,
                ];
            });

        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to = min($currentPage * $this->perPage, $totalItems);

        $properties = \App\Models\Property::where('type', 'hotel')
            ->get()
            ->mapWithKeys(function ($property) {
                $name = is_array($property->name) ? ($property->name['en'] ?? $property->name['ar'] ?? 'Unknown') : $property->name;
                return [$property->id => $name];
            })
            ->toArray();

        return compact('totalItems', 'lastPage', 'currentPage', 'eventPages', 'from', 'to', 'properties');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Content Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-sparkles';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Meetings & Events Pages';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Meetings & Events Pages';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Meetings & Events Pages';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Manage all Meetings & Events pages displayed across the HMH Hotel Group website.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addEventPage')
                ->label('Add Event Page')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getEventFormSchema())
                ->url(\App\Filament\Pages\MeetingsAndEvents\CreateMeetingsAndEvent::getUrl())
                ->action(function (array $data) {
                    Notification::make()
                        ->title('Meetings & Events page saved successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewEventPageAction(): Action
    {
        return Action::make('viewEventPage')
            ->modalHeading('View Event Page')
            ->modalWidth('7xl')
            ->form($this->getEventFormSchema())
            ->fillForm(function (array $arguments) {
                $page = \App\Models\MeetingEventPage::find($arguments['id']);
                if (!$page)
                    return [];
                return [
                    'title' => is_array($page->title) ? ($page->title['en'] ?? reset($page->title)) : $page->title,
                    'event_type' => ucfirst(str_replace('_', ' ', $page->type)),
                    'status' => $page->is_active ? 'Published' : 'Draft',
                    'description' => is_array($page->description) ? ($page->description['en'] ?? reset($page->description)) : $page->description,
                ];
            })
            ->disabledForm()
            ->url(fn(array $arguments) => \App\Filament\Pages\MeetingsAndEvents\ViewMeetingsAndEvent::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(fn() => null);
    }

    public function editEventPageAction(): Action
    {
        return Action::make('editEventPage')
            ->modalHeading('Edit Event Page')
            ->modalWidth('7xl')
            ->form($this->getEventFormSchema())
            ->fillForm(function (array $arguments) {
                $page = \App\Models\MeetingEventPage::find($arguments['id']);
                if (!$page)
                    return [];
                return [
                    'title' => is_array($page->title) ? ($page->title['en'] ?? reset($page->title)) : $page->title,
                    'event_type' => ucfirst(str_replace('_', ' ', $page->type)),
                    'status' => $page->is_active ? 'Published' : 'Draft',
                    'description' => is_array($page->description) ? ($page->description['en'] ?? reset($page->description)) : $page->description,
                ];
            })
            ->url(fn(array $arguments) => \App\Filament\Pages\MeetingsAndEvents\EditMeetingsAndEvent::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data, array $arguments) {
                $page = \App\Models\MeetingEventPage::find($arguments['id']);
                if ($page) {
                    $page->title = $data['title'] ?? $page->title;
                    $page->description = $data['description'] ?? $page->description;
                    $page->details_content = $data['description'] ?? $page->details_content;
                    $page->is_active = ($data['status'] ?? 'Published') === 'Published';
                    $page->save();
                }
                Notification::make()
                    ->title('Meetings & Events page saved successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteEventPageAction(): Action
    {
        return Action::make('deleteEventPage')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                \App\Models\MeetingEventPage::find($arguments['id'])?->delete();
                Notification::make()
                    ->title('Event page deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public static function getEventFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    \Filament\Schemas\Components\Tabs::make('Tabs')
                        ->tabs([
                            \Filament\Schemas\Components\Tabs\Tab::make('Venue Details')
                                ->schema([
                                    TextInput::make('title')
                                        ->label('Venue / Page Title')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn(string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug($state))),
                                    Select::make('property_id')
                                        ->label('Associated Hotel')
                                        ->options(function () {
                                            return \App\Models\Property::where('type', 'hotel')
                                                ->get()
                                                ->mapWithKeys(function ($property) {
                                                    $name = is_array($property->name) ? ($property->name['en'] ?? $property->name['ar'] ?? 'Unknown') : $property->name;
                                                    return [$property->id => $name];
                                                })
                                                ->toArray();
                                        })
                                        ->default(function () {
                                            return \App\Models\Property::where('type', 'hotel')->where('slug', 'opera-grand-hotel')->value('id')
                                                ?? \App\Models\Property::where('type', 'hotel')->value('id');
                                        })
                                        ->required(),
                                    Select::make('event_type')
                                        ->label('Event Type')
                                        ->options([
                                            'Main Overview' => 'Main Overview',
                                            'Corporate Meetings' => 'Corporate Meetings',
                                            'Weddings' => 'Weddings',
                                            'Conference Facilities' => 'Conference Facilities',
                                            'Banquet Halls' => 'Banquet Halls',
                                            'Private Events' => 'Private Events',
                                            'Outdoor Venues' => 'Outdoor Venues',
                                        ])
                                        ->required(),
                                    TextInput::make('slug')
                                        ->label('Slug')
                                        ->required(),
                                    Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'Published' => 'Published',
                                            'Draft' => 'Draft',
                                        ])
                                        ->default('Published')
                                        ->required(),
                                    \App\Filament\Forms\Components\JoditEditor::make('description')
                                        ->label('Venue Description')
                                        ->required(),
                                    Textarea::make('highlights')
                                        ->label('Highlights Text')
                                        ->placeholder('Optional text displayed in the Highlights section below the image slider')
                                        ->rows(3),
                                    TextInput::make('rfp_url')
                                        ->label('Request for Proposal URL')
                                        ->placeholder('/contact or https://...'),
                                    Section::make('Contact Details')
                                        ->description('Contact information displayed on the venue detail page')
                                        ->schema([
                                            Grid::make(2)->schema([
                                                TextInput::make('contact_phone')
                                                    ->label('Contact Phone')
                                                    ->tel()
                                                    ->placeholder('+971 4 224 8587'),
                                                TextInput::make('contact_email')
                                                    ->label('Contact Email')
                                                    ->email()
                                                    ->placeholder('events.coraldeira@hmhhotelgroup.com'),
                                            ]),
                                        ]),
                                ]),
                            \Filament\Schemas\Components\Tabs\Tab::make('Slider & Gallery Images')
                                ->schema([
                                    Section::make('Venue Images')
                                        ->description('Upload high-resolution images for the venue slider and gallery')
                                        ->schema([
                                            FileUpload::make('gallery')
                                                ->label('Slider & Gallery Images')
                                                ->image()
                                                ->multiple()
                                                ->reorderable()
                                                ->disk('uploads')
                                                ->directory(''),
                                        ]),
                                ]),
                        ]),
                ])->columnSpan(2),

                Grid::make(1)->schema([
                    Section::make('SEO Settings')
                        ->schema([
                            TextInput::make('meta_title')
                                ->label('Meta Title'),
                            Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->rows(3),
                            TextInput::make('meta_keywords')
                                ->label('Meta Keywords'),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    // Mock Data removed
}
