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
            ->fillForm(fn (array $arguments) => $this->getMockEventPages()[$arguments['id']] ?? [])
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
            ->fillForm(fn (array $arguments) => $this->getMockEventPages()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\MeetingsAndEvents\EditMeetingsAndEvent::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
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
            ->action(function () {
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
                    Section::make('Basic Information')
                        ->schema([
                            TextInput::make('title')
                                ->label('Page Title')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $set('slug', Str::slug($state))),
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
                        ]),

                    Section::make('Content')
                        ->schema([
                            TextInput::make('banner_title')
                                ->label('Banner Title'),
                            TextInput::make('banner_subtitle')
                                ->label('Banner Subtitle'),
                            \App\Filament\Forms\Components\JoditEditor::make('description')
                                ->label('Rich Text Description'),
                            TextInput::make('venue_capacity')
                                ->label('Venue Capacity')
                                ->numeric(),
                            TagsInput::make('facilities')
                                ->label('Facilities Included')
                                ->placeholder('Add facilities (e.g., Projector, WiFi)'),
                            Grid::make(2)->schema([
                                TextInput::make('cta_text')
                                    ->label('Call-to-Action Button Text'),
                                TextInput::make('cta_url')
                                    ->label('Call-to-Action URL')
                                    ->url(),
                            ]),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
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

    public static function getMockEventPages(): array
    {
        return [
            1 => ['id' => 1, 'title' => 'Executive Boardroom', 'hotel' => 'Opera Hotel', 'event_type' => 'Corporate Meetings', 'status' => 'Published', 'last_updated' => '2023-10-15', 'venue_capacity' => '20', 'cta_text' => 'Book Now'],
            2 => ['id' => 2, 'title' => 'Grand Crystal Ballroom', 'hotel' => 'Coral Dubai Deira Hotel', 'event_type' => 'Weddings', 'status' => 'Published', 'last_updated' => '2023-10-18', 'venue_capacity' => '500', 'cta_text' => 'Inquire Now'],
            3 => ['id' => 3, 'title' => 'Oasis Convention Center', 'hotel' => 'Coral Beach Resort Sharjah', 'event_type' => 'Conference Facilities', 'status' => 'Published', 'last_updated' => '2023-10-20', 'venue_capacity' => '1000', 'cta_text' => 'Plan Your Event'],
            4 => ['id' => 4, 'title' => 'Sapphire Banquet Hall', 'hotel' => 'EWA Hotel Apartments', 'event_type' => 'Banquet Halls', 'status' => 'Draft', 'last_updated' => '2023-10-22', 'venue_capacity' => '150', 'cta_text' => 'Learn More'],
            5 => ['id' => 5, 'title' => 'Rooftop Lounge', 'hotel' => 'ECOS Dubai Hotel', 'event_type' => 'Private Events', 'status' => 'Published', 'last_updated' => '2023-10-25', 'venue_capacity' => '80', 'cta_text' => 'Reserve Space'],
            6 => ['id' => 6, 'title' => 'Beachfront Gardens', 'hotel' => 'Coral Beach Resort Sharjah', 'event_type' => 'Outdoor Venues', 'status' => 'Published', 'last_updated' => '2023-10-28', 'venue_capacity' => '300', 'cta_text' => 'Book Venue'],
        ];
    }
}
