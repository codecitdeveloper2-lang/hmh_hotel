<?php

namespace App\Filament\Pages\Hotels\Amenities;

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
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;

class ListAmenities extends Page
{
    use HasHotelTabs;

    protected string $view = 'filament.pages.manage-amenities';

    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;
    protected static ?string $slug = 'manage-hotels/{record}/amenities';
    protected static bool $shouldRegisterNavigation = false;

    public function mount($record): void
    {
        $this->mountHasHotelTabs($record);
    }

    public $viewType = 'table';
    public $searchQuery = '';
    public $filterCategory = '';
    public $filterStatus = '';

    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterCategory(): void { $this->currentPage = 1; }
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

    public function getTitle(): string | Htmlable
    {
        return 'Hotel Amenities';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Hotel Amenities';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage hotel amenities displayed on the website.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addAmenity')
                ->label('Add Amenity')
                ->icon('heroicon-o-plus')
                ->url(fn () => \App\Filament\Pages\Hotels\Amenities\CreateAmenity::getUrl(['record' => $this->record])),
        ];
    }

    public function viewAmenityAction(): Action
    {
        return Action::make('viewAmenity')
            ->modalHeading('View Amenity')
            ->modalWidth('7xl')
            ->form($this->getAmenityFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockAmenities()[$arguments['id']] ?? [])
            ->disabledForm()
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\Amenities\ViewAmenity::getUrl(['record' => $this->record, 'amenity_id' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editAmenityAction(): Action
    {
        return Action::make('editAmenity')
            ->modalHeading('Edit Amenity')
            ->modalWidth('7xl')
            ->form($this->getAmenityFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockAmenities()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Hotels\Amenities\EditAmenity::getUrl(['record' => $this->record, 'amenity_id' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Amenity Updated')
                    ->body('The amenity details have been updated successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public function deleteAmenityAction(): Action
    {
        return Action::make('deleteAmenity')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Amenity Deleted')
                    ->body('The amenity has been deleted successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public static function getAmenityFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            TextInput::make('title')
                                ->label('Title')
                                ->required(),
                            TextInput::make('subtitle')
                                ->label('Subtitle'),
                            TextInput::make('button_label')
                                ->label('Button Label'),
                            TextInput::make('button_link')
                                ->label('Button Link')
                                ->url(),
                            TextInput::make('display_order')
                                ->label('Display Order')
                                ->numeric()
                                ->default(0),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'Active' => 'Active',
                                    'Inactive' => 'Inactive',
                                ])
                                ->default('Active')
                                ->required(),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            FileUpload::make('images')
                                ->label('Multiple Image Upload')
                                ->image()
                                ->multiple()
                                ->panelLayout('grid')
                                ->required(),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    public static function getMockAmenities(): array
    {
        return [
            1 => ['id' => 1, 'title' => 'Main Entrance', 'hotel' => 'Coral Beach Resort Sharjah', 'category' => 'Hotel Exterior', 'display_order' => 1, 'status' => 'Active', 'last_updated' => '2023-10-01 10:00:00'],
            2 => ['id' => 2, 'title' => 'Grand Lobby', 'hotel' => 'Coral Dubai Deira Hotel', 'category' => 'Lobby', 'display_order' => 1, 'status' => 'Active', 'last_updated' => '2023-10-02 11:30:00'],
            3 => ['id' => 3, 'title' => 'Deluxe Sea View', 'hotel' => 'ECOS Dubai Hotel', 'category' => 'Guest Rooms', 'display_order' => 2, 'status' => 'Active', 'last_updated' => '2023-10-05 09:15:00'],
            4 => ['id' => 4, 'title' => 'Royal Suite Bedroom', 'hotel' => 'EWA Hotel Apartments', 'category' => 'Suites', 'display_order' => 1, 'status' => 'Active', 'last_updated' => '2023-10-06 14:20:00'],
            5 => ['id' => 5, 'title' => 'Ocean Grill Restaurant', 'hotel' => 'Opera Hotel', 'category' => 'Restaurant', 'display_order' => 3, 'status' => 'Active', 'last_updated' => '2023-10-10 16:45:00'],
            6 => ['id' => 6, 'title' => 'Infinity Pool View', 'hotel' => 'Coral Beach Resort Sharjah', 'category' => 'Swimming Pool', 'display_order' => 1, 'status' => 'Inactive', 'last_updated' => '2023-10-12 08:30:00'],
            7 => ['id' => 7, 'title' => 'Fitness Center', 'hotel' => 'Coral Dubai Deira Hotel', 'category' => 'Gym', 'display_order' => 2, 'status' => 'Active', 'last_updated' => '2023-10-15 12:00:00'],
            8 => ['id' => 8, 'title' => 'Ayurvedic Massage Room', 'hotel' => 'ECOS Dubai Hotel', 'category' => 'Spa', 'display_order' => 1, 'status' => 'Active', 'last_updated' => '2023-10-18 10:10:00'],
            9 => ['id' => 9, 'title' => 'Majestic Ballroom', 'hotel' => 'EWA Hotel Apartments', 'category' => 'Conference Hall', 'display_order' => 1, 'status' => 'Active', 'last_updated' => '2023-10-20 15:55:00'],
            10 => ['id' => 10, 'title' => 'Wedding Setup', 'hotel' => 'Opera Hotel', 'category' => 'Events', 'display_order' => 4, 'status' => 'Active', 'last_updated' => '2023-10-25 09:40:00'],
        ];
    }
}
