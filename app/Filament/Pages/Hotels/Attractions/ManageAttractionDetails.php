<?php
namespace App\Filament\Pages\Hotels\Attractions;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ManageAttractionDetails extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.manage-attraction-details';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/attractions/{attraction_id}/details-content';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public $record;
    public $attraction_id;
    public ?array $data = [];

    public function mount($record, $attraction_id): void
    {
        $this->record = $record;
        $this->attraction_id = $attraction_id;
        
        $mockData = \App\Filament\Pages\Hotels\Attractions\ListAttractions::getMockAttractions();
        $attractionData = $mockData[$this->attraction_id] ?? [];
        
        $this->form->fill([
            'title' => $attractionData['name'] ?? '',
        ]);
        $this->form->fill($this->data);
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    public function form($form)
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make('Attraction Content')
                ->schema([
                    \Filament\Schemas\Components\Grid::make(3)->schema([
                        \Filament\Schemas\Components\Grid::make(1)->schema([
                            \Filament\Forms\Components\TextInput::make('title')
                                ->label('Title')
                                ->required(),
                            \App\Filament\Forms\Components\JoditEditor::make('description')
                                ->label('Description'),
                        ])->columnSpan(2),
                        
                        \Filament\Schemas\Components\Grid::make(1)->schema([
                            \Filament\Forms\Components\Textarea::make('address')
                                ->label('Address')
                                ->rows(3),
                            \Filament\Forms\Components\TextInput::make('google_maps')
                                ->label('Google Maps URL')
                                ->url(),
                        ])->columnSpan(1),
                    ]),
                ]),

            \Filament\Schemas\Components\Section::make('Media')
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('images')
                        ->label('Images')
                        ->multiple()
                        ->image()
                        ->reorderable()
                        ->panelLayout('grid')
                        ->columnSpanFull(),
                ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\Attractions\EditAttraction::getUrl(['record' => $this->record, 'attraction_id' => $this->attraction_id]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
