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

    protected function getViewData(): array
    {
        $query = \App\Models\Offer::query();
        
        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        
        $offers = $query->skip(($currentPage - 1) * $this->perPage)
                        ->take($this->perPage)
                        ->get()
                        ->map(function ($offer) {
                            return [
                                'id' => $offer->id,
                                'title' => $offer->name,
                                'hotel' => 'All Hotels',
                                'offer_type' => 'Seasonal',
                                'status' => $offer->is_active ? 'Active' : 'Inactive',
                                'last_updated' => $offer->updated_at?->format('Y-m-d H:i') ?? '',
                                'valid_from' => $offer->valid_from,
                                'valid_until' => $offer->valid_to,
                            ];
                        });
                        
        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'offers', 'from', 'to');
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
                    \App\Models\Offer::create([
                        'name' => $data['title'] ?? 'New Offer',
                        'slug' => \Illuminate\Support\Str::slug($data['title'] ?? 'offer-' . time()),
                        'description' => $data['highlight_description'] ?? null,
                        'is_active' => ($data['status'] ?? 'Active') === 'Active' ? 1 : 0,
                    ]);
                    Notification::make()
                        ->title('Offer Created successfully.')
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
            ->fillForm(function (array $arguments) {
                $offer = \App\Models\Offer::find($arguments['id']);
                if (!$offer) return [];
                return [
                    'title' => $offer->name,
                    'status' => $offer->is_active ? 'Active' : 'Inactive',
                    'highlight_description' => $offer->description,
                ];
            })
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Offers\EditOffer::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data, array $arguments) {
                \App\Models\Offer::find($arguments['id'])?->update([
                    'name' => $data['title'] ?? 'New Offer',
                    'slug' => \Illuminate\Support\Str::slug($data['title'] ?? 'offer-' . time()),
                    'description' => $data['highlight_description'] ?? null,
                    'is_active' => ($data['status'] ?? 'Active') === 'Active' ? 1 : 0,
                ]);
                Notification::make()
                    ->title('Offer Updated successfully.')
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
            ->action(function (array $arguments) {
                \App\Models\Offer::find($arguments['id'])?->delete();
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

                    \Filament\Schemas\Components\Tabs::make('Tabs')
                        ->tabs([
                            \Filament\Schemas\Components\Tabs\Tab::make('General Information')
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
                            TextInput::make('highlight_title')
                                ->label('Offer Highlight Title')
                                ->default('GREAT OFFERS ARE JUST A CLICK'),
                            TextInput::make('highlight_subtitle')
                                ->label('Offer Highlight Subtitle')
                                ->default('Unbeatable packages for your holidays'),
                            \App\Filament\Forms\Components\JoditEditor::make('highlight_description')
                                ->label('Offer Highlight Description')
                                ->default('Elevate your stay with exclusive offers designed to enhance every moment of your journey.'),
                                ]),
                            \Filament\Schemas\Components\Tabs\Tab::make('Banner')
                                ->schema([
                            TextInput::make('banner_title')
                                ->label('Banner Title')
                                ->default('VIEW OFFERS'),

                            FileUpload::make('banner_image')
                                ->label('Banner Image Upload')
                                ->image(),
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
