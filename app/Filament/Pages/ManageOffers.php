<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class ManageOffers extends Page
{
    protected string $view = 'filament.pages.manage-offers';

    public $searchQuery = '';
    public $filterHotel = '';
    public $filterType = '';
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';


    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterHotel(): void { $this->currentPage = 1; }
    public function updatedFilterType(): void { $this->currentPage = 1; }
    public function updatedFilterStatus(): void { $this->currentPage = 1; }
    public function updatedFilterDateFrom(): void { $this->currentPage = 1; }
    public function updatedFilterDateTo(): void { $this->currentPage = 1; }
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
        return 2;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-tag';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Offer Management';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Offer Management';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Offer Management';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Create and manage promotional offers available across HMH Hotel Group properties.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addOffer')
                ->label('Add Offer')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getOfferFormSchema())
                
            ->url(\App\Filament\Pages\Offers\CreateOffer::getUrl())
            ->action(function (array $data) {
                    Notification::make()
                        ->title('Offer saved successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewOfferAction(): Action
    {
        return Action::make('viewOffer')
            ->modalHeading('View Offer')
            ->modalWidth('7xl')
            ->form($this->getOfferFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockOffers()[$arguments['id']] ?? [])
            ->disabledForm()
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Offers\ViewOffer::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editOfferAction(): Action
    {
        return Action::make('editOffer')
            ->modalHeading('Edit Offer')
            ->modalWidth('7xl')
            ->form($this->getOfferFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockOffers()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Offers\EditOffer::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Offer saved successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteOfferAction(): Action
    {
        return Action::make('deleteOffer')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Offer deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public static function getOfferFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            TextInput::make('title')
                                ->label('Offer Title')
                                ->required(),
                            Select::make('hotel')
                                ->label('Hotel')
                                ->options([
                                    'Coral Beach Resort Sharjah' => 'Coral Beach Resort Sharjah',
                                    'Coral Dubai Deira Hotel' => 'Coral Dubai Deira Hotel',
                                    'ECOS Dubai Hotel' => 'ECOS Dubai Hotel',
                                    'EWA Hotel Apartments' => 'EWA Hotel Apartments',
                                    'Opera Hotel' => 'Opera Hotel',
                                ])
                                ->required(),
                            Select::make('offer_type')
                                ->label('Offer Type')
                                ->options([
                                    'Seasonal' => 'Seasonal',
                                    'Weekend' => 'Weekend',
                                    'Family' => 'Family',
                                    'Corporate' => 'Corporate',
                                    'Honeymoon' => 'Honeymoon',
                                    'Long Stay' => 'Long Stay',
                                ])
                                ->required(),
                            \App\Filament\Forms\Components\JoditEditor::make('short_description')
                                ->label('Short Description'),
                            \App\Filament\Forms\Components\JoditEditor::make('detailed_description')
                                ->label('Detailed Description'),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'Active' => 'Active',
                                    'Inactive' => 'Inactive',
                                    'Draft' => 'Draft',
                                    'Expired' => 'Expired',
                                ])
                                ->default('Active')
                                ->required(),
                        ]),

                    Section::make('Offer Validity')
                        ->schema([
                            Grid::make(2)->schema([
                                DatePicker::make('valid_from')
                                    ->label('Valid From'),
                                DatePicker::make('valid_until')
                                    ->label('Valid Until'),
                            ]),
                            TextInput::make('booking_period')
                                ->label('Booking Period (e.g., Book by 30th Nov)'),
                        ]),
                        
                    Section::make('Pricing')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('discount_type')
                                    ->label('Discount Type')
                                    ->options([
                                        'Percentage' => 'Percentage',
                                        'Fixed Amount' => 'Fixed Amount',
                                    ]),
                                TextInput::make('discount_value')
                                    ->label('Discount Value')
                                    ->numeric(),
                            ]),
                            TextInput::make('promo_code')
                                ->label('Promo Code'),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            FileUpload::make('banner_image')
                                ->label('Banner Image Upload')
                                ->image(),
                            FileUpload::make('gallery')
                                ->label('Gallery Upload')
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

    public static function getMockOffers(): array
    {
        return [
            1 => ['id' => 1, 'title' => 'Summer Escape', 'hotel' => 'Coral Beach Resort Sharjah', 'offer_type' => 'Seasonal', 'valid_from' => '2023-06-01', 'valid_until' => '2023-08-31', 'promo_code' => 'SUMMER23', 'status' => 'Expired', 'last_updated' => '2023-05-15', 'discount_type' => 'Percentage', 'discount_value' => '20', 'short_description' => 'Enjoy your summer...'],
            2 => ['id' => 2, 'title' => 'Weekend Getaway', 'hotel' => 'Coral Dubai Deira Hotel', 'offer_type' => 'Weekend', 'valid_from' => '2023-11-01', 'valid_until' => '2023-12-31', 'promo_code' => 'WKND15', 'status' => 'Active', 'last_updated' => '2023-10-20', 'discount_type' => 'Percentage', 'discount_value' => '15', 'short_description' => 'Relax this weekend...'],
            3 => ['id' => 3, 'title' => 'Family Stay Package', 'hotel' => 'ECOS Dubai Hotel', 'offer_type' => 'Family', 'valid_from' => '2023-11-15', 'valid_until' => '2024-01-15', 'promo_code' => 'FAM2023', 'status' => 'Active', 'last_updated' => '2023-10-25', 'discount_type' => 'Fixed Amount', 'discount_value' => '500', 'short_description' => 'Fun for the whole family...'],
            4 => ['id' => 4, 'title' => 'Honeymoon Special', 'hotel' => 'EWA Hotel Apartments', 'offer_type' => 'Honeymoon', 'valid_from' => '2023-01-01', 'valid_until' => '2024-12-31', 'promo_code' => 'LOVE', 'status' => 'Active', 'last_updated' => '2023-01-10', 'discount_type' => 'Percentage', 'discount_value' => '25', 'short_description' => 'Romantic getaway...'],
            5 => ['id' => 5, 'title' => 'Business Traveller Offer', 'hotel' => 'Opera Hotel', 'offer_type' => 'Corporate', 'valid_from' => '2023-09-01', 'valid_until' => '2024-03-31', 'promo_code' => 'BIZ10', 'status' => 'Draft', 'last_updated' => '2023-10-10', 'discount_type' => 'Percentage', 'discount_value' => '10', 'short_description' => 'For the working professional...'],
            6 => ['id' => 6, 'title' => 'Early Bird Discount', 'hotel' => 'Coral Beach Resort Sharjah', 'offer_type' => 'Seasonal', 'valid_from' => '2024-01-01', 'valid_until' => '2024-03-31', 'promo_code' => 'EARLY24', 'status' => 'Active', 'last_updated' => '2023-10-28', 'discount_type' => 'Percentage', 'discount_value' => '30', 'short_description' => 'Book early and save...'],
        ];
    }
}
