<?php
namespace App\Filament\Pages\Hotels\DiningOutlets;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ManageDiningDetails extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.manage-dining-details';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/dining-outlets/{outlet_id}/details-content';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public $record;
    public $outlet_id;
    public ?array $data = [];

    public function mount($record, $outlet_id): void
    {
        $this->record = $record;
        $this->outlet_id = $outlet_id;
        
        $mockData = \App\Filament\Pages\Hotels\DiningOutlets\ListDiningOutlets::getMockDiningOutlets();
        $outletData = $mockData[$this->outlet_id] ?? [];
        
        $this->form->fill([
            'title' => $outletData['name'] ?? '',
            'cuisine_type' => $outletData['cuisine_type'] ?? '',
            'book_table_label' => 'BOOK A TABLE',
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
            \Filament\Schemas\Components\Section::make('Main Content')
                ->schema([
                    \Filament\Schemas\Components\Grid::make(3)->schema([
                        \Filament\Schemas\Components\Grid::make(1)->schema([
                            \Filament\Forms\Components\TextInput::make('title')
                                ->label('Title')
                                ->required(),
                            \App\Filament\Forms\Components\JoditEditor::make('description')
                                ->label('Description'),
                            \Filament\Forms\Components\TextInput::make('cuisine_type')
                                ->label('Style Cuisine'),
                        ])->columnSpan(2),
                        
                        \Filament\Schemas\Components\Grid::make(1)->schema([
                            \Filament\Forms\Components\Repeater::make('contact_details')
                                ->label('Contact Details')
                                ->schema([
                                    \Filament\Forms\Components\TextInput::make('icon')
                                        ->label('Icon Name (e.g. heroicon-o-phone)')
                                        ->default('heroicon-o-phone'),
                                    \Filament\Forms\Components\TextInput::make('contact_text')
                                        ->label('Contact Info')
                                        ->required(),
                                ])
                                ->defaultItems(1)
                                ->collapsible()
                                ->cloneable(),
                        ])->columnSpan(1),
                    ]),
                ]),
            
            \Filament\Schemas\Components\Section::make('Call to Action')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('book_table_label')
                        ->label('Button Label')
                        ->default('BOOK A TABLE'),
                    \Filament\Forms\Components\TextInput::make('book_table_link')
                        ->label('Button Link'),
                ])->columns(2),

            \Filament\Schemas\Components\Section::make('Bottom Gallery')
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('gallery')
                        ->label('Gallery Images')
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

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\DiningOutlets\EditDiningOutlet::getUrl(['record' => $this->record, 'outlet_id' => $this->outlet_id]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
