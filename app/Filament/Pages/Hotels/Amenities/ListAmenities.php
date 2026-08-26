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
            1 => ['id' => 1, 'title' => 'Spa', 'hotel' => 'Bahi Ajman Palace Hotel', 'category' => 'Spa', 'image' => 'https://image-tc.galaxy.tf/wisvg-f62nu2f0nr7onprvogd18wnn/spa_logo.svg', 'display_order' => 1, 'status' => 'Active', 'last_updated' => now()->format('Y-m-d H:i:s')],
            2 => ['id' => 2, 'title' => 'Fitness Centre', 'hotel' => 'Bahi Ajman Palace Hotel', 'category' => 'Gym', 'image' => 'https://image-tc.galaxy.tf/wisvg-8frobb5iq8j189we29jvxkd6e/fitness-centre_logo.svg', 'display_order' => 2, 'status' => 'Active', 'last_updated' => now()->format('Y-m-d H:i:s')],
            3 => ['id' => 3, 'title' => 'Swimming Pool', 'hotel' => 'Bahi Ajman Palace Hotel', 'category' => 'Swimming Pool', 'image' => 'https://image-tc.galaxy.tf/wisvg-uq7n4u2813p78ld7hsvq0fj5/swimming-pool_logo.svg', 'display_order' => 3, 'status' => 'Active', 'last_updated' => now()->format('Y-m-d H:i:s')],
            4 => ['id' => 4, 'title' => 'Health Club Membership', 'hotel' => 'Bahi Ajman Palace Hotel', 'category' => 'Gym', 'display_order' => 4, 'status' => 'Active', 'last_updated' => now()->format('Y-m-d H:i:s')],
            5 => ['id' => 5, 'title' => 'Day Beach Access', 'hotel' => 'Bahi Ajman Palace Hotel', 'category' => 'Hotel Exterior', 'display_order' => 5, 'status' => 'Active', 'last_updated' => now()->format('Y-m-d H:i:s')],
            6 => ['id' => 6, 'title' => 'Swimming Lessons', 'hotel' => 'Bahi Ajman Palace Hotel', 'category' => 'Swimming Pool', 'display_order' => 6, 'status' => 'Active', 'last_updated' => now()->format('Y-m-d H:i:s')],
            7 => ['id' => 7, 'title' => 'Kids Club', 'hotel' => 'Bahi Ajman Palace Hotel', 'category' => 'Events', 'display_order' => 7, 'status' => 'Active', 'last_updated' => now()->format('Y-m-d H:i:s')],
        ];
    }
}
