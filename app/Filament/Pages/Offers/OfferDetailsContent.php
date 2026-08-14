<?php
namespace App\Filament\Pages\Offers;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class OfferDetailsContent extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-offers/{record}/details-content';

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        
        $mockData = \App\Filament\Pages\ManageOffers::getMockOffers();
        // Fallback to empty array if record doesn't exist
        $this->form->fill($mockData[$this->record] ?? []);
        $this->form->fill($this->data);
    }

    public function getSubNavigation(): array
    {
        return [];
    }

    public function form($form)
    {
        return $form->schema([
            \Filament\Schemas\Components\Section::make('General')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->label('Title'),
                    \App\Filament\Forms\Components\JoditEditor::make('description')
                        ->label('Description'),
                ]),

            \Filament\Schemas\Components\Section::make('Images')
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('images')
                        ->label('Images')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->columnSpanFull(),
                ]),

            \Filament\Schemas\Components\Section::make('Offer Valid')
                ->schema([
                    \Filament\Schemas\Components\Grid::make(2)->schema([
                        \Filament\Forms\Components\DatePicker::make('valid_from')
                            ->label('Valid From'),
                        \Filament\Forms\Components\DatePicker::make('valid_until')
                            ->label('Valid Until'),
                    ]),
                    \Filament\Forms\Components\TextInput::make('booking_period')
                        ->label('Booking Period (e.g., Book by 30th Nov)'),
                ]),

            \Filament\Schemas\Components\Section::make('Pricing')
                ->schema([
                    \Filament\Schemas\Components\Grid::make(2)->schema([
                        \Filament\Forms\Components\Select::make('discount_type')
                            ->label('Discount Type')
                            ->options([
                                'Percentage' => 'Percentage',
                                'Fixed Amount' => 'Fixed Amount',
                            ]),
                        \Filament\Forms\Components\TextInput::make('discount_value')
                            ->label('Discount Value')
                            ->numeric(),
                    ]),
                    \Filament\Forms\Components\TextInput::make('promo_code')
                        ->label('Promo Code'),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Offers\EditOffer::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
