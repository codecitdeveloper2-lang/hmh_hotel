<?php
namespace App\Filament\Pages\Hotels\Amenities;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class AmenityDetailsContent extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.amenity-details-content';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-hotels/{record}/amenities/{amenity_id}/details-content';
    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;

    public $record;
    public $amenity_id;
    public ?array $data = [];

    public function mount($record, $amenity_id): void
    {
        $this->record = $record;
        $this->amenity_id = $amenity_id;
        
        $mockData = \App\Filament\Pages\Hotels\Amenities\ListAmenities::getMockAmenities();
        $this->form->fill($mockData[$this->amenity_id] ?? []);
        $this->form->fill($this->data);
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    public function form($form)
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make('Banner')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('banner_title')
                        ->label('Banner Title'),
                    \Filament\Forms\Components\FileUpload::make('banner_images')
                        ->label('Banner Images')
                        ->multiple()
                        ->image()
                        ->reorderable()
                        ->columnSpanFull(),
                ]),
            
            \Filament\Schemas\Components\Section::make('Highlight Section')
                ->description('Content displayed directly below the banner.')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('highlight_subtitle')
                        ->label('Subtitle'),
                    \Filament\Forms\Components\TextInput::make('highlight_title')
                        ->label('Title'),
                    \Filament\Forms\Components\Textarea::make('highlight_description')
                        ->label('Description')
                        ->rows(4)
                        ->columnSpanFull(),
                ])->columns(2),
                
            \Filament\Schemas\Components\Section::make('Facilities Carousel')
                ->description('Manage the individual facilities shown in the slider.')
                ->schema([
                    \Filament\Forms\Components\Repeater::make('facilities')
                        ->label('Facilities')
                        ->schema([
                            \Filament\Forms\Components\FileUpload::make('image')
                                ->label('Background Image')
                                ->image()
                                ->required()
                                ->columnSpanFull(),
                            \Filament\Forms\Components\TextInput::make('title')
                                ->label('Title')
                                ->required(),
                            \Filament\Forms\Components\Textarea::make('description')
                                ->label('Description')
                                ->rows(3)
                                ->columnSpanFull(),
                            \Filament\Forms\Components\TextInput::make('button_label')
                                ->label('Button Label')
                                ->placeholder('e.g., CALL US'),
                            \Filament\Forms\Components\TextInput::make('button_link')
                                ->label('Button Link')
                                ->placeholder('e.g., tel:+123456789'),
                        ])
                        ->columns(2)
                        ->reorderable()
                        ->collapsible()
                        ->defaultItems(1)
                        ->columnSpanFull(),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Hotels\Amenities\EditAmenity::getUrl(['record' => $this->record, 'amenity_id' => $this->amenity_id]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
