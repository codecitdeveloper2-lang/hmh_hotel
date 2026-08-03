<?php

namespace App\Filament\Pages;

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

class ManageOurLocations extends Page
{
    protected string $view = 'filament.pages.manage-our-locations';

    public $searchQuery = '';
    public $filterDestination = '';

    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterDestination(): void { $this->currentPage = 1; }
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
        return 0; // Above Destination Management
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-map';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Our Location';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Our Location Management';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Our Location Management';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage Our Location entries for HMH Hotel Group.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addLocation')
                ->label('Add Location')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getLocationFormSchema())
                ->url(\App\Filament\Pages\OurLocations\CreateLocation::getUrl())
                ->action(function (array $data) {
                    Notification::make()
                        ->title('Location saved successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewLocationAction(): Action
    {
        return Action::make('viewLocation')
            ->modalHeading('View Location')
            ->modalWidth('7xl')
            ->form($this->getLocationFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockOurLocations()[$arguments['id']] ?? [])
            ->disabledForm()
            ->url(fn (array $arguments) => \App\Filament\Pages\OurLocations\ViewLocation::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editLocationAction(): Action
    {
        return Action::make('editLocation')
            ->modalHeading('Edit Location')
            ->modalWidth('7xl')
            ->form($this->getLocationFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockOurLocations()[$arguments['id']] ?? [])
            ->url(fn (array $arguments) => \App\Filament\Pages\OurLocations\EditLocation::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Location saved successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteLocationAction(): Action
    {
        return Action::make('deleteLocation')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Location deleted successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public static function getLocationFormSchema(): array
    {
        $destinations = collect(\App\Filament\Pages\ManageDestinations::getMockDestinations())
            ->pluck('name', 'id')
            ->toArray();

        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            TextInput::make('city_name')
                                ->label('City Name')
                                ->required(),
                            Select::make('destination_id')
                                ->label('Destination')
                                ->options($destinations)
                                ->required(),
                            Textarea::make('home_teaser')
                                ->label('Home Teaser')
                                ->rows(4),
                            Grid::make(2)->schema([
                                Toggle::make('featured_on_home')
                                    ->label('Featured on Home')
                                    ->default(false),
                                TextInput::make('display_order')
                                    ->label('Display Order')
                                    ->numeric()
                                    ->default(0),
                            ]),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Media')
                        ->schema([
                            FileUpload::make('home_image')
                                ->label('Home Image Upload')
                                ->image(),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    public static function getMockOurLocations(): array
    {
        return [
            1 => ['id' => 1, 'city_name' => 'Dubai', 'destination_id' => 1, 'home_teaser' => 'Experience the magic of Dubai.', 'featured_on_home' => true, 'display_order' => 1, 'home_image' => null],
            2 => ['id' => 2, 'city_name' => 'Riyadh', 'destination_id' => 2, 'home_teaser' => 'Discover the heart of Saudi Arabia.', 'featured_on_home' => false, 'display_order' => 2, 'home_image' => null],
            3 => ['id' => 3, 'city_name' => 'Manama', 'destination_id' => 3, 'home_teaser' => 'Explore the beauty of Bahrain.', 'featured_on_home' => true, 'display_order' => 3, 'home_image' => null],
        ];
    }
}
