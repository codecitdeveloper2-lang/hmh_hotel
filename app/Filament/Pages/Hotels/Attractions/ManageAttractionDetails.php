<?php
namespace App\Filament\Pages\Hotels\Attractions;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ManageAttractionDetails extends Page implements HasForms
{
    use HasHotelTabs;
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
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $this->attraction_id = $attraction_id;
        $a = \App\Models\Attraction::find($this->attraction_id);
        if ($a) {
            $this->form->fill([
                'title' => is_array($a->name) ? ($a->name['en'] ?? '') : $a->name,
                'description' => is_array($a->description) ? ($a->description['en'] ?? '') : $a->description,
                'address' => $a->address,
                'google_maps' => $a->google_maps_url,
            ]);
        }
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
                    \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                        ->collection('attraction_gallery')
                        ->label('Images')
                        ->multiple()
                        ->image()
                        ->reorderable()
                        ->panelLayout('grid')
                        ->columnSpanFull(),
                ]),
        ])
        ->statePath('data')
        ->model(\App\Models\Attraction::find($this->attraction_id));
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $a = \App\Models\Attraction::find($this->attraction_id);
        if ($a) {
            $name = is_array($a->name) ? $a->name : ['en' => $a->name];
            $name['en'] = $data['title'] ?? '';

            $desc = is_array($a->description) ? $a->description : ['en' => $a->description];
            $desc['en'] = $data['description'] ?? '';

            $a->update([
                'name' => $name,
                'description' => $desc,
                'address' => $data['address'] ?? null,
                'google_maps_url' => $data['google_maps'] ?? null,
            ]);
            
            $this->form->model($a)->saveRelationships();
        }
        
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\Attractions\EditAttraction::getUrl(['record' => $this->record, 'attraction_id' => $this->attraction_id]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\Attractions\EditAttraction::getUrl(['record' => $this->record, 'attraction_id' => $this->attraction_id]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
