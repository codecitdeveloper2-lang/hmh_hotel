<?php

namespace App\Filament\Pages\Hotels\FAQs;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;

class ListFaqs extends Page
{
    use HasHotelTabs;

    protected string $view = 'filament.pages.manage-faqs';

    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;
    protected static ?string $slug = 'manage-hotels/{record}/faqs';
    protected static bool $shouldRegisterNavigation = false;

    public function mount($record): void
    {
        $this->mountHasHotelTabs($record);
    }


    public int $perPage = 10;
    public int $currentPage = 1;

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
        $hotelName = \App\Filament\Pages\ManageHotels::getMockHotels()[$this->record]['name'] ?? '';
        $all         = collect($this->getMockFaqs())->filter(fn($item) => $item['hotel'] === $hotelName);
        $totalItems  = $all->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        $faqs        = $all->forPage($currentPage, $this->perPage);
        $from        = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to          = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'faqs', 'from', 'to');
    }



    public function getTitle(): string | Htmlable
    {
        return 'FAQs Management';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'FAQs Management';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage hotel-specific frequently asked questions.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addFaq')
                ->label('Add FAQ')
                ->icon('heroicon-o-plus')
                ->modalWidth('4xl')
                ->form($this->getFaqFormSchema())
                
            ->url(fn () => \App\Filament\Pages\Hotels\FAQs\CreateFaq::getUrl(['record' => $this->record]))
            ->action(function (array $data) {
                    Notification::make()
                        ->title('FAQ Created')
                        ->body('The FAQ has been created successfully (Mock).')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewFaqAction(): Action
    {
        return Action::make('viewFaq')
            ->modalHeading('View FAQ')
            ->modalWidth('4xl')
            ->form($this->getFaqFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockFaqs()[$arguments['id']] ?? [])
            ->disabledForm()
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\FAQs\ViewFaq::getUrl(['record' => $this->record, 'faq_id' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editFaqAction(): Action
    {
        return Action::make('editFaq')
            ->modalHeading('Edit FAQ')
            ->modalWidth('4xl')
            ->form($this->getFaqFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockFaqs()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\FAQs\EditFaq::getUrl(['record' => $this->record, 'faq_id' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('FAQ Updated')
                    ->body('The FAQ has been updated successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public function deleteFaqAction(): Action
    {
        return Action::make('deleteFaq')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('FAQ Deleted')
                    ->body('The FAQ has been deleted successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public static function getFaqFormSchema(): array
    {
        return [
            Grid::make(1)->schema([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('hotel')
                                ->label('Hotel')
                                ->options([
                                    'coral-beach-resort-sharjah' => 'Coral Beach Resort Sharjah',
                                    'coral-dubai-deira-hotel' => 'Coral Dubai Deira Hotel',
                                    'ecos-dubai-hotel' => 'ECOS Dubai Hotel',
                                    'ewa-hotel-apartments' => 'EWA Hotel Apartments',
                                    'opera-hotel' => 'Opera Hotel',
                                ])
                                ->required(),
                            Select::make('category')
                                ->label('Category')
                                ->options([
                                    'general' => 'General',
                                    'booking' => 'Booking',
                                    'rooms' => 'Rooms',
                                    'dining' => 'Dining',
                                    'facilities' => 'Facilities',
                                    'policies' => 'Policies',
                                ])
                                ->required(),
                        ]),
                        TextInput::make('question')
                            ->label('Question')
                            ->required(),
                        RichEditor::make('answer')
                            ->label('Answer')
                            ->required(),
                        Grid::make(2)->schema([
                            TextInput::make('display_order')
                                ->label('Display Order')
                                ->numeric()
                                ->default(0),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                ])
                                ->default('active')
                                ->required(),
                        ]),
                    ]),
                    
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title'),
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(3),
                    ]),
            ]),
        ];
    }

    public static function getMockFaqs(): array
    {
        return [
            1 => [
                'id' => 1,
                'question' => 'What is the check-in time?',
                'answer' => 'Our standard check-in time is 14:00 (2:00 PM).',
                'hotel' => 'Coral Beach Resort Sharjah',
                'category' => 'Policies',
                'display_order' => 1,
                'status' => 'Active',
                'last_updated' => '2023-10-15 09:30',
            ],
            2 => [
                'id' => 2,
                'question' => 'What is the check-out time?',
                'answer' => 'Our standard check-out time is 12:00 PM (Noon).',
                'hotel' => 'Coral Dubai Deira Hotel',
                'category' => 'Policies',
                'display_order' => 2,
                'status' => 'Active',
                'last_updated' => '2023-10-16 11:45',
            ],
            3 => [
                'id' => 3,
                'question' => 'Is free Wi-Fi available?',
                'answer' => 'Yes, we offer complimentary high-speed Wi-Fi in all rooms and public areas.',
                'hotel' => 'Opera Hotel',
                'category' => 'Facilities',
                'display_order' => 3,
                'status' => 'Active',
                'last_updated' => '2023-10-17 14:20',
            ],
            4 => [
                'id' => 4,
                'question' => 'Do you provide airport transfers?',
                'answer' => 'Yes, airport transfers can be arranged at an additional cost. Please contact the concierge.',
                'hotel' => 'ECOS Dubai Hotel',
                'category' => 'General',
                'display_order' => 4,
                'status' => 'Active',
                'last_updated' => '2023-10-18 10:15',
            ],
            5 => [
                'id' => 5,
                'question' => 'Is breakfast included?',
                'answer' => 'Breakfast inclusion depends on the room rate selected during booking.',
                'hotel' => 'Coral Beach Resort Sharjah',
                'category' => 'Dining',
                'display_order' => 5,
                'status' => 'Active',
                'last_updated' => '2023-10-19 16:50',
            ],
            6 => [
                'id' => 6,
                'question' => 'Are pets allowed?',
                'answer' => 'Unfortunately, we do not allow pets on the premises.',
                'hotel' => 'Coral Beach Resort Sharjah',
                'category' => 'Policies',
                'display_order' => 6,
                'status' => 'Active',
                'last_updated' => '2023-10-20 08:15',
            ],
            7 => [
                'id' => 7,
                'question' => 'Is parking available?',
                'answer' => 'Yes, complimentary valet and self-parking are available for all guests.',
                'hotel' => 'EWA Hotel Apartments',
                'category' => 'Facilities',
                'display_order' => 7,
                'status' => 'Active',
                'last_updated' => '2023-10-21 13:40',
            ],
            8 => [
                'id' => 8,
                'question' => 'Do you have a swimming pool?',
                'answer' => 'Yes, we have a temperature-controlled outdoor swimming pool.',
                'hotel' => 'Opera Hotel',
                'category' => 'Facilities',
                'display_order' => 8,
                'status' => 'Active',
                'last_updated' => '2023-10-22 17:10',
            ],
            9 => [
                'id' => 9,
                'question' => 'Is early check-in available?',
                'answer' => 'Early check-in is subject to availability and may incur an additional charge.',
                'hotel' => 'ECOS Dubai Hotel',
                'category' => 'Policies',
                'display_order' => 9,
                'status' => 'Active',
                'last_updated' => '2023-10-23 09:00',
            ],
            10 => [
                'id' => 10,
                'question' => 'Can I cancel my reservation?',
                'answer' => 'Cancellations can be made up to 24 hours prior to arrival without penalty for standard rates.',
                'hotel' => 'Coral Dubai Deira Hotel',
                'category' => 'Booking',
                'display_order' => 10,
                'status' => 'Active',
                'last_updated' => '2023-10-24 10:30',
            ],
        ];
    }
}
