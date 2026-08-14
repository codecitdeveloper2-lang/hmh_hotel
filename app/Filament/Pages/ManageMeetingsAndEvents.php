<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
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

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterHotel(): void { $this->currentPage = 1; }
    public function updatedFilterEventType(): void { $this->currentPage = 1; }
    public function updatedFilterStatus(): void { $this->currentPage = 1; }
    public function updatedPerPage(): void { $this->currentPage = 1; }

    protected function getViewData(): array
    {
        $query = \App\Models\MeetingEventPage::query();

        if ($this->searchQuery) {
            $query->where('title', 'like', "%{$this->searchQuery}%");
        }

        if ($this->filterEventType) {
            $query->where('type', $this->filterEventType);
        }

        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));

        $eventPages = $query->skip(($currentPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($page) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'hotel' => $page->property?->display_name ?? 'Unknown',
                    'event_type' => ucfirst(str_replace('_', ' ', $page->type)),
                    'status' => $page->is_active ? 'Published' : 'Draft',
                    'last_updated' => $page->updated_at?->format('Y-m-d') ?? '',
                    'venue_capacity' => $page->capacity_details ?? 'N/A',
                    'cta_text' => 'Book Now',
                ];
            });

        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'eventPages', 'from', 'to');
    }

        public static function getNavigationGroup(): ?string
    {
        return 'Content Management';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
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

    public function getTitle(): string | Htmlable
    {
        return 'Meetings & Events Pages';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Meetings & Events Pages';
    }

    public function getSubheading(): string | Htmlable | null
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
                if (!$page) return [];
                return [
                    'title' => $page->title,
                    'event_type' => ucfirst(str_replace('_', ' ', $page->type)),
                    'status' => $page->is_active ? 'Published' : 'Draft',
                    'highlight_description' => $page->description,
                ];
            })
            ->disabledForm()
            
            ->url(fn (array $arguments) => \App\Filament\Pages\MeetingsAndEvents\ViewMeetingsAndEvent::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editEventPageAction(): Action
    {
        return Action::make('editEventPage')
            ->modalHeading('Edit Event Page')
            ->modalWidth('7xl')
            ->form($this->getEventFormSchema())
            ->fillForm(function (array $arguments) {
                $page = \App\Models\MeetingEventPage::find($arguments['id']);
                if (!$page) return [];
                return [
                    'title' => $page->title,
                    'event_type' => ucfirst(str_replace('_', ' ', $page->type)),
                    'status' => $page->is_active ? 'Published' : 'Draft',
                    'highlight_description' => $page->description,
                ];
            })
            
            ->url(fn (array $arguments) => \App\Filament\Pages\MeetingsAndEvents\EditMeetingsAndEvent::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data, array $arguments) {
                $page = \App\Models\MeetingEventPage::find($arguments['id']);
                if ($page) {
                    $page->title = $data['title'] ?? $page->title;
                    $page->description = $data['highlight_description'] ?? $page->description;
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
                            \Filament\Schemas\Components\Tabs\Tab::make('General Information')
                                ->schema([
                            TextInput::make('title')
                                ->label('Page Title')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, \Filament\Schemas\Components\Utilities\Set $set) => $set('slug', Str::slug($state))),
                            Select::make('hotel')
                                ->label('Associated Hotel')
                                ->options([
                                    'Coral Beach Resort Sharjah' => 'Coral Beach Resort Sharjah',
                                    'Coral Dubai Deira Hotel' => 'Coral Dubai Deira Hotel',
                                    'ECOS Dubai Hotel' => 'ECOS Dubai Hotel',
                                    'EWA Hotel Apartments' => 'EWA Hotel Apartments',
                                    'Opera Hotel' => 'Opera Hotel',
                                ])
                                ->required(),
                            Select::make('event_type')
                                ->label('Event Type')
                                ->options([
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
                            TextInput::make('highlight_subtitle')
                                ->label('Meetings Subtitle'),
                            TextInput::make('highlight_title')
                                ->label('Meetings Title'),
                            \App\Filament\Forms\Components\JoditEditor::make('highlight_description')
                                ->label('Meetings Description'),
                                ]),
                            \Filament\Schemas\Components\Tabs\Tab::make('Banner')
                                ->schema([
                            TextInput::make('banner_title')
                                ->label('Banner Title'),
                            Section::make('Media')
                                ->schema([
                                    FileUpload::make('banner_image')
                                        ->label('Banner Image')
                                        ->image(),
                                    FileUpload::make('gallery')
                                        ->label('Gallery Images')
                                        ->image()
                                        ->multiple(),
                                ]),
                                ]),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([

                    Section::make('SEO')
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
