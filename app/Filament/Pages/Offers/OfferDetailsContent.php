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
        
        $offer = \App\Models\Offer::find($record);
        if ($offer) {
            $this->form->fill($offer->toArray());
        }
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
                    \Filament\Forms\Components\TextInput::make('name.en')
                        ->label('Title')
                        ->required(),
                    \App\Filament\Forms\Components\JoditEditor::make('details_content.en')
                        ->label('Description'),
                ]),

            \Filament\Schemas\Components\Section::make('Images')
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('images')
                        ->label('Images')
                        ->disk('uploads')
                        ->directory('offer-details')
                        ->image()
                        ->multiple()
                        ->reorderable()
                        ->columnSpanFull(),
                ]),




        ])->statePath('data');
    }

    public function save(): void
    {
        $offer = \App\Models\Offer::findOrFail($this->record);
        $offer->update($this->form->getState());
        
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
    }

    public function getBackUrl(): string { return \App\Filament\Pages\Offers\EditOffer::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
