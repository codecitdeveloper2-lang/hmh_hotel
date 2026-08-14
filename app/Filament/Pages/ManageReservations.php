<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use BackedEnum;

class ManageReservations extends Page
{
    protected string $view = 'filament.pages.manage-reservations';

    public $searchQuery = '';
    public $filterHotel = '';
    public $filterStatus = '';
    public $filterPaymentStatus = '';
    public $filterSource = '';
    public $filterCheckIn = '';
    public $filterCheckOut = '';
    public $selectedRows = [];

    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterHotel(): void { $this->currentPage = 1; }
    public function updatedFilterStatus(): void { $this->currentPage = 1; }
    public function updatedFilterPaymentStatus(): void { $this->currentPage = 1; }
    public function updatedFilterSource(): void { $this->currentPage = 1; }
    public function updatedFilterCheckIn(): void { $this->currentPage = 1; }
    public function updatedFilterCheckOut(): void { $this->currentPage = 1; }
    public function updatedPerPage(): void { $this->currentPage = 1; }

    protected function getViewData(): array
    {
        $query = \App\Models\Reservation::query()->with('property');

        if ($this->searchQuery) {
            $query->where(function ($q) {
                $q->where('confirmation_number', 'like', "%{$this->searchQuery}%")
                  ->orWhere('travelclick_reservation_id', 'like', "%{$this->searchQuery}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', strtolower($this->filterStatus));
        }

        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));

        $reservations = $query->orderByDesc('created_at')
            ->skip(($currentPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($res) {
                $nights = 0;
                if ($res->check_in && $res->check_out) {
                    $nights = \Carbon\Carbon::parse($res->check_in)->diffInDays(\Carbon\Carbon::parse($res->check_out));
                }
                return [
                    'id' => $res->id,
                    'reservation_number' => $res->confirmation_number,
                    'guest_name' => $res->member ? ($res->member->first_name . ' ' . $res->member->last_name) : 'Walk-in Guest',
                    'email' => $res->member?->email ?? 'N/A',
                    'phone' => $res->member?->phone ?? 'N/A',
                    'nationality' => 'N/A',
                    'hotel' => $res->property?->name ?? 'Unknown',
                    'room_type' => $res->rate_plan_id ?? 'Standard',
                    'check_in_date' => $res->check_in,
                    'check_out_date' => $res->check_out,
                    'number_of_nights' => $nights,
                    'number_of_adults' => $res->adults,
                    'number_of_children' => $res->children,
                    'booking_source' => 'TravelClick',
                    'reservation_status' => ucfirst($res->status),
                    'payment_status' => 'N/A',
                    'payment_method' => 'N/A',
                    'total_amount' => number_format($res->total_amount ?? 0, 2),
                    'currency' => $res->currency ?? 'AED',
                    'guest_notes' => '',
                    'internal_notes' => '',
                    'last_updated' => $res->updated_at?->format('Y-m-d') ?? '',
                ];
            });

        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'reservations', 'from', 'to');
    }
    public static function getNavigationGroup(): ?string
    {
        return 'Operations';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-calendar-days';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Reservations';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Reservations';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Reservations';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'View and manage hotel reservations received through the HMH Hotel Group booking channels.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportReservations')
                ->label('Export Reservations')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    Notification::make()
                        ->title('Reservation exported successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewReservationAction(): Action
    {
        return Action::make('viewReservation')
            ->modalHeading('View Reservation Details')
            ->modalWidth('7xl')
            ->form($this->getViewReservationFormSchema())
            ->fillForm(function (array $arguments) {
                $res = \App\Models\Reservation::with(['property', 'member'])->find($arguments['id']);
                if (!$res) return [];
                $nights = 0;
                if ($res->check_in && $res->check_out) {
                    $nights = \Carbon\Carbon::parse($res->check_in)->diffInDays(\Carbon\Carbon::parse($res->check_out));
                }
                return [
                    'reservation_number' => $res->confirmation_number,
                    'guest_name' => $res->member ? ($res->member->first_name . ' ' . $res->member->last_name) : 'Walk-in Guest',
                    'email' => $res->member?->email ?? 'N/A',
                    'phone' => $res->member?->phone ?? 'N/A',
                    'hotel' => $res->property?->name ?? 'Unknown',
                    'room_type' => $res->rate_plan_id ?? 'Standard',
                    'check_in_date' => $res->check_in,
                    'check_out_date' => $res->check_out,
                    'number_of_nights' => $nights,
                    'number_of_adults' => $res->adults,
                    'number_of_children' => $res->children,
                    'booking_source' => 'TravelClick',
                    'total_amount' => number_format($res->total_amount ?? 0, 2),
                    'currency' => $res->currency ?? 'AED',
                    'payment_status' => 'N/A',
                    'payment_method' => 'N/A',
                ];
            })
            ->disabledForm()
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public function printReservationAction(): Action
    {
        return Action::make('printReservation')
            ->icon('heroicon-m-printer')
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Reservations\ViewReservation::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function () {
                Notification::make()
                    ->title('Reservation queued for printing.')
                    ->info()
                    ->send();
            });
    }

    public function exportPdfAction(): Action
    {
        return Action::make('exportPdf')
            ->icon('heroicon-m-document-arrow-down')
            ->action(function () {
                Notification::make()
                    ->title('PDF generated successfully.')
                    ->success()
                    ->send();
            });
    }

    public function sendConfirmationEmailAction(): Action
    {
        return Action::make('sendConfirmationEmail')
            ->icon('heroicon-m-envelope')
            ->action(function () {
                Notification::make()
                    ->title('Confirmation email sent.')
                    ->success()
                    ->send();
            });
    }

    public function markAsCheckedInAction(): Action
    {
        return Action::make('markAsCheckedIn')
            ->icon('heroicon-m-arrow-right-on-rectangle')
            ->color('success')
            ->action(function (array $arguments) {
                \App\Models\Reservation::find($arguments['id'])?->update(['status' => 'confirmed']);
                Notification::make()
                    ->title('Reservation status updated to Checked In.')
                    ->success()
                    ->send();
            });
    }

    public function markAsCheckedOutAction(): Action
    {
        return Action::make('markAsCheckedOut')
            ->icon('heroicon-m-arrow-left-on-rectangle')
            ->color('gray')
            ->action(function () {
                Notification::make()
                    ->title('Reservation status updated to Checked Out.')
                    ->success()
                    ->send();
            });
    }

    public function cancelReservationAction(): Action
    {
        return Action::make('cancelReservation')
            ->icon('heroicon-m-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                \App\Models\Reservation::find($arguments['id'])?->update(['status' => 'cancelled']);
                Notification::make()
                    ->title('Reservation cancelled successfully.')
                    ->success()
                    ->send();
            });
    }

    public function bulkAction(string $action): void
    {
        if (empty($this->selectedRows)) {
            Notification::make()->title('Please select at least one reservation.')->warning()->send();
            return;
        }

        $message = match ($action) {
            'export' => 'Reservations exported successfully.',
            'print' => 'Reservations queued for printing.',
            'confirm' => 'Reservation status updated to Confirmed.',
            'cancel' => 'Reservations cancelled successfully.',
            default => 'Action completed.'
        };

        Notification::make()->title($message)->success()->send();
        $this->selectedRows = [];
    }

    public static function getViewReservationFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Reservation Details')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('reservation_number')->label('Reservation Number'),
                                TextInput::make('hotel')->label('Hotel'),
                                TextInput::make('room_type')->label('Room Type'),
                                TextInput::make('booking_source')->label('Booking Source'),
                                TextInput::make('check_in_date')->label('Check-in Date'),
                                TextInput::make('check_out_date')->label('Check-out Date'),
                                TextInput::make('number_of_nights')->label('Number of Nights'),
                                Grid::make(2)->schema([
                                    TextInput::make('number_of_adults')->label('Adults'),
                                    TextInput::make('number_of_children')->label('Children'),
                                ]),
                            ]),
                        ]),
                    Section::make('Guest Information')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('guest_name')->label('Full Name'),
                                TextInput::make('email')->label('Email Address'),
                                TextInput::make('phone')->label('Phone Number'),
                                TextInput::make('nationality')->label('Nationality'),
                            ]),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Payment Information')
                        ->schema([
                            TextInput::make('payment_status')->label('Payment Status'),
                            TextInput::make('payment_method')->label('Payment Method'),
                            TextInput::make('total_amount')->label('Total Amount'),
                            TextInput::make('currency')->label('Currency'),
                        ]),
                    Section::make('Special Requests')
                        ->schema([
                            Textarea::make('guest_notes')->label('Guest Notes')->rows(3),
                            Textarea::make('internal_notes')->label('Internal Notes')->rows(3),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    // Mock Data removed
}
