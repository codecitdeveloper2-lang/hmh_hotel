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
use App\Models\OurLocation;

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

    protected function getViewData(): array
    {
        $query = OurLocation::query();
        
        if (!empty($this->searchQuery)) {
            $query->where('city_name', 'like', '%' . $this->searchQuery . '%');
        }
        
        if (!empty($this->filterDestination)) {
            $query->where('destination_id', $this->filterDestination);
        }
        
        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        
        $locations = $query->orderBy('display_order', 'asc')
            ->skip(($currentPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'city_name' => $location->city_name,
                    'destination_id' => $location->destination_id,
                    'home_image' => $location->home_image,
                    'featured_on_home' => $location->featured_on_home,
                    'display_order' => $location->display_order,
                    'last_updated' => $location->updated_at?->format('Y-m-d') ?? '',
                ];
            });
            
        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'locations', 'from', 'to');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addLocation')
                ->label('Add Location')
                ->icon('heroicon-o-plus')
                ->modalWidth('7xl')
                ->form($this->getLocationFormSchema())
                ->action(function (array $data) {
                    OurLocation::create($data);
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
            ->fillForm(fn (array $arguments) => OurLocation::find($arguments['id'])?->toArray() ?? [])
            ->disabledForm()
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public function editLocationAction(): Action
    {
        return Action::make('editLocation')
            ->modalHeading('Edit Location')
            ->modalWidth('7xl')
            ->form($this->getLocationFormSchema())
            ->fillForm(fn (array $arguments) => OurLocation::find($arguments['id'])?->toArray() ?? [])
            ->action(function (array $data, array $arguments) {
                OurLocation::find($arguments['id'])?->update($data);
                Notification::make()
                    ->title('Location updated successfully.')
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
            ->action(function (array $arguments) {
                OurLocation::find($arguments['id'])?->delete();
                Notification::make()
                    ->title('Location deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public static function getLocationFormSchema(): array
    {
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
                                ->options(fn () => \App\Models\Destination::all()->pluck('name', 'id')->toArray())
                                ->searchable()
                                ->nullable(),
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
                                ->disk('uploads')
                                ->image(),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }
}

