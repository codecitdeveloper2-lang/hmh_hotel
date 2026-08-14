<?php
namespace App\Filament\Pages\MeetingsAndEvents;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;

class MeetingsAndEventDetailsContent extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.generic-create-edit';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'manage-meetings-and-events/{record}/details-content';

    public $record;
    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        
        $mockData = \App\Filament\Pages\ManageMeetingsAndEvents::getMockEventPages();
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
            \Filament\Schemas\Components\Section::make('General Details')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->label('Title')
                        ->default('CORPORATE MEETINGS'),
                    \App\Filament\Forms\Components\JoditEditor::make('description')
                        ->label('Rich Text Description'),
                    \Filament\Forms\Components\TextInput::make('venue_capacity')
                        ->label('Venue Capacity')
                        ->numeric(),
                    \Filament\Forms\Components\TagsInput::make('facilities')
                        ->label('Facilities Included')
                        ->placeholder('Add facilities (e.g., Projector, WiFi)'),
                ]),

            \Filament\Schemas\Components\Section::make('Contact Details')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('contact_phone')
                        ->label('Contact Phone')
                        ->placeholder('+971 6 522 9999'),
                ]),

            \Filament\Schemas\Components\Section::make('Call to Action')
                ->schema([
                    \Filament\Schemas\Components\Grid::make(2)->schema([
                        \Filament\Forms\Components\TextInput::make('cta_text')
                            ->label('Call-to-Action Button Text')
                            ->default('REQUEST FOR PROPOSAL'),
                        \Filament\Forms\Components\TextInput::make('cta_url')
                            ->label('Call-to-Action URL')
                            ->url(),
                    ]),
                ]),

            \Filament\Schemas\Components\Section::make('Media')
                ->schema([
                    \Filament\Forms\Components\FileUpload::make('image')
                        ->label('Image')
                        ->image()
                        ->columnSpanFull(),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        \Filament\Notifications\Notification::make()->title('Updated successfully')->success()->send();
    }

    public function getBackUrl(): string { return \App\Filament\Pages\MeetingsAndEvents\EditMeetingsAndEvent::getUrl(['record' => $this->record]); }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }
}
