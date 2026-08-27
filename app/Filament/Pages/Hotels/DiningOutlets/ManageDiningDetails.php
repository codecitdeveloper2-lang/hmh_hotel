<?php
namespace App\Filament\Pages\Hotels\DiningOutlets;

use Filament\Pages\Page;
use App\Filament\Pages\Hotels\Traits\HasHotelTabs;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class ManageDiningDetails extends Page implements HasForms
{
    use HasHotelTabs;
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
        $this->mountHasHotelTabs($record);
        $this->record = $record;
        $this->outlet_id = $outlet_id;
        $d = \App\Models\DiningOutlet::find($this->outlet_id);
        if ($d) {
            $this->form->fill([
                'title' => is_array($d->name) ? ($d->name['en'] ?? '') : $d->name,
                'description' => is_array($d->description) ? ($d->description['en'] ?? '') : $d->description,
                'cuisine_type' => is_array($d->cuisine_type) ? ($d->cuisine_type['en'] ?? '') : $d->cuisine_type,
                'contact_details' => $d->contact_details ?? [],
                'book_table_label' => $d->book_table_label ?? 'BOOK A TABLE',
                'book_table_link' => $d->book_table_link ?? '',
            ]);
        }
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
                    \Filament\Forms\Components\SpatieMediaLibraryFileUpload::make('gallery')
                        ->disk('uploads')
                        ->collection('dining_gallery')
                        ->label('Gallery Images')
                        ->multiple()
                        ->image()
                        ->reorderable()
                        ->panelLayout('grid')
                        ->columnSpanFull(),
                ]),
        ])
        ->statePath('data')
        ->model(\App\Models\DiningOutlet::find($this->outlet_id));
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $d = \App\Models\DiningOutlet::find($this->outlet_id);
        if ($d) {
            $name = is_array($d->name) ? $d->name : ['en' => $d->name];
            $name['en'] = $data['title'] ?? '';

            $desc = is_array($d->description) ? $d->description : ['en' => $d->description];
            $desc['en'] = $data['description'] ?? '';

            $cuisine = is_array($d->cuisine_type) ? $d->cuisine_type : ['en' => $d->cuisine_type];
            $cuisine['en'] = $data['cuisine_type'] ?? '';

            $d->update([
                'name' => $name,
                'description' => $desc,
                'cuisine_type' => $cuisine,
                'contact_details' => $data['contact_details'] ?? null,
                'book_table_label' => $data['book_table_label'] ?? null,
                'book_table_link' => $data['book_table_link'] ?? null,
            ]);
            
            $this->form->model($d)->saveRelationships();
        }
        
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        $this->redirect(\App\Filament\Pages\Hotels\DiningOutlets\EditDiningOutlet::getUrl(['record' => $this->record, 'outlet_id' => $this->outlet_id]));
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\DiningOutlets\EditDiningOutlet::getUrl(['record' => $this->record, 'outlet_id' => $this->outlet_id]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
