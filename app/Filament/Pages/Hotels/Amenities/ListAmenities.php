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

class ListAmenities extends Page implements \Filament\Forms\Contracts\HasForms
{
    use HasHotelTabs;
    use \Filament\Forms\Concerns\InteractsWithForms;

    protected string $view = 'filament.pages.generic-create-edit';

    protected static ?string $cluster = \App\Filament\Clusters\HotelManagement\HotelManagementCluster::class;
    protected static ?string $slug = 'manage-hotels/{record}/amenities';
    protected static bool $shouldRegisterNavigation = false;

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->mountHasHotelTabs($record);
        $this->record = $record;

        $a = \App\Models\Amenity::firstOrCreate(
            ['property_id' => $record],
            [
                'title' => ['en' => 'Amenities'],
                'is_active' => 1,
                'display_order' => 0
            ]
        );

        $this->form->fill([
            'title' => is_array($a->title) ? ($a->title['en'] ?? '') : $a->title,
            'description' => $a->description,
            'read_more_label' => $a->read_more_label,
            'read_more_link' => $a->read_more_link,
            'call_us_no' => $a->call_us_no,
            'amenities_list' => is_string($a->amenities_list) ? json_decode($a->amenities_list, true) : ($a->amenities_list ?? []),
            'gallery' => is_string($a->gallery) ? json_decode($a->gallery, true) : ($a->gallery ?? []),
        ]);
    }

    public function form($form)
    {
        return $form->schema(self::getAmenityFormSchema())->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $a = \App\Models\Amenity::where('property_id', $this->record)->first();
        if ($a) {
            $a->update([
                'title' => ['en' => $data['title']],
                'description' => $data['description'] ?? null,
                'read_more_label' => $data['read_more_label'] ?? null,
                'read_more_link' => $data['read_more_link'] ?? null,
                'call_us_no' => $data['call_us_no'] ?? null,
                'amenities_list' => $data['amenities_list'] ?? [],
                'gallery' => $data['gallery'] ?? [],
            ]);
            \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
        }
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
        return 'Manage the amenities and facilities section for this property.';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getBackUrl(): string
    {
        return \App\Filament\Pages\Hotels\EditHotel::getUrl(['record' => $this->record]);
    }

    public static function getAmenityFormSchema(): array
    {
        return [
            Section::make('General Content')
                ->schema([
                    TextInput::make('title')
                        ->label('Title')
                        ->required(),
                    Textarea::make('description')
                        ->label('Description')
                        ->rows(4),
                    TextInput::make('read_more_label')
                        ->label('Read More Label'),
                    TextInput::make('read_more_link')
                        ->label('Read More Link'),
                    TextInput::make('call_us_no')
                        ->label('Call Us No'),
                ])->columns(2),
            
            Section::make('Amenities List')
                ->schema([
                    \Filament\Forms\Components\Repeater::make('amenities_list')
                        ->label('Amenities Items')
                        ->schema([
                            \Filament\Forms\Components\FileUpload::make('icon')
                                ->label('Icon / Image Upload')
                                ->disk('uploads')
                                ->directory('')
                                ->image()
                                ->required(),
                            TextInput::make('name')
                                ->label('Amenity Name')
                                ->required(),
                            Textarea::make('description')
                                ->label('Amenity Description')
                                ->rows(3),
                        ])
                        ->columns(1)
                        ->collapsible()
                        ->reorderable()
                        ->defaultItems(1),
                ]),
        ];
    }
}

