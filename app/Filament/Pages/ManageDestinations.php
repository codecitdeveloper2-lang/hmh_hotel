<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class ManageDestinations extends Page
{
    protected string $view = 'filament.pages.manage-destinations';

    public $searchQuery = '';
    public $filterCountry = '';
    public $filterStatus = '';


    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterCountry(): void { $this->currentPage = 1; }
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
        return 1;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-map-pin';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Destination Management';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Destination Management';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Destination Management';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage destinations where HMH Hotel Group hotels are located.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addDestination')
                ->label('Add Destination')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getDestinationFormSchema())
                
            ->url(\App\Filament\Pages\Destinations\CreateDestination::getUrl())
            ->action(function (array $data) {
                    Notification::make()
                        ->title('Destination saved successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewDestinationAction(): Action
    {
        return Action::make('viewDestination')
            ->modalHeading('View Destination')
            ->modalWidth('7xl')
            ->form($this->getDestinationFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockDestinations()[$arguments['id']] ?? [])
            ->disabledForm()
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Destinations\ViewDestination::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(fn () => null);
    }

    public function editDestinationAction(): Action
    {
        return Action::make('editDestination')
            ->modalHeading('Edit Destination')
            ->modalWidth('7xl')
            ->form($this->getDestinationFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockDestinations()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Destinations\EditDestination::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Destination saved successfully.')
                    ->success()
                    ->send();
            });
    }

    public function deleteDestinationAction(): Action
    {
        return Action::make('deleteDestination')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Destination deleted successfully (Mock).')
                    ->success()
                    ->send();
            });
    }

    public static function getDestinationFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Basic Information')
                        ->schema([
                            TextInput::make('name')
                                ->label('Destination Name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required(),
                            Select::make('country')
                                ->label('Country')
                                ->options([
                                    'United Arab Emirates' => 'United Arab Emirates',
                                    'Saudi Arabia' => 'Saudi Arabia',
                                    'Bahrain' => 'Bahrain',
                                    'Oman' => 'Oman',
                                    'Qatar' => 'Qatar',
                                ])
                                ->required(),
                            \App\Filament\Forms\Components\JoditEditor::make('description')
                                ->label('Description'),
                            Grid::make(2)->schema([
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        'Active' => 'Active',
                                        'Inactive' => 'Inactive',
                                    ])
                                    ->default('Active')
                                    ->required(),
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
                            FileUpload::make('banner_image')
                                ->label('Banner Image Upload')
                                ->image(),
                            FileUpload::make('thumbnail_image')
                                ->label('Thumbnail Image Upload')
                                ->image(),
                        ]),
                        
                    Section::make('SEO')
                        ->schema([
                            TextInput::make('meta_title')
                                ->label('Meta Title'),
                            Textarea::make('meta_description')
                                ->label('Meta Description')
                                ->rows(2),
                            TextInput::make('meta_keywords')
                                ->label('Meta Keywords'),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    public static function getMockDestinations(): array
    {
        return [
            1 => ['id' => 1, 'name' => 'United Arab Emirates', 'slug' => 'united-arab-emirates', 'country' => 'United Arab Emirates', 'hotels_count' => 12, 'status' => 'Active', 'display_order' => 1, 'last_updated' => '2023-10-20', 'description' => 'A vibrant destination...'],
            2 => ['id' => 2, 'name' => 'Saudi Arabia', 'slug' => 'saudi-arabia', 'country' => 'Saudi Arabia', 'hotels_count' => 8, 'status' => 'Active', 'display_order' => 2, 'last_updated' => '2023-10-21', 'description' => 'The kingdom destination...'],
            3 => ['id' => 3, 'name' => 'Bahrain', 'slug' => 'bahrain', 'country' => 'Bahrain', 'hotels_count' => 3, 'status' => 'Active', 'display_order' => 3, 'last_updated' => '2023-10-22', 'description' => 'Modern island capital...'],
            4 => ['id' => 4, 'name' => 'Oman', 'slug' => 'oman', 'country' => 'Oman', 'hotels_count' => 5, 'status' => 'Inactive', 'display_order' => 4, 'last_updated' => '2023-10-23', 'description' => 'Historical coastal destination...'],
            5 => ['id' => 5, 'name' => 'Qatar', 'slug' => 'qatar', 'country' => 'Qatar', 'hotels_count' => 6, 'status' => 'Active', 'display_order' => 5, 'last_updated' => '2023-10-24', 'description' => 'Fastest growing destination...'],
        ];
    }
}
