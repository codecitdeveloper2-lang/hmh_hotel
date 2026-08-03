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

    public function nextPage(int $lastPage): void
    {
        if ($this->currentPage < $lastPage) $this->currentPage++;
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) $this->currentPage--;
    }

    public function gotoPage(int $page): void
    {
        $this->currentPage = $page;
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
            ->fillForm(fn (array $arguments) => $this->getMockReservations()[$arguments['id']] ?? [])
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
            ->action(function () {
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
            ->action(function () {
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

    public static function getMockReservations(): array
    {
        return [
            1 => ['id' => 1, 'reservation_number' => 'RES-2023-11001', 'guest_name' => 'John Smith', 'email' => 'john.smith@example.com', 'phone' => '+971 50 123 4567', 'nationality' => 'United Kingdom', 'hotel' => 'Coral Beach Resort Sharjah', 'room_type' => 'Deluxe Sea View', 'check_in_date' => '2023-11-20', 'check_out_date' => '2023-11-25', 'number_of_nights' => 5, 'number_of_adults' => 2, 'number_of_children' => 0, 'booking_source' => 'Website', 'reservation_status' => 'Confirmed', 'payment_status' => 'Paid', 'payment_method' => 'Credit Card', 'total_amount' => '3,500', 'currency' => 'AED', 'guest_notes' => 'High floor preferred. Anniversary trip.', 'internal_notes' => 'Arrange anniversary cake in room.', 'last_updated' => '2023-11-15'],
            2 => ['id' => 2, 'reservation_number' => 'RES-2023-11002', 'guest_name' => 'Sarah Jenkins', 'email' => 's.jenkins@example.co.uk', 'phone' => '+44 7700 900123', 'nationality' => 'United Kingdom', 'hotel' => 'Bahi Ajman Palace Hotel', 'room_type' => 'Executive Suite', 'check_in_date' => '2023-11-16', 'check_out_date' => '2023-11-19', 'number_of_nights' => 3, 'number_of_adults' => 2, 'number_of_children' => 1, 'booking_source' => 'Booking.com', 'reservation_status' => 'Checked In', 'payment_status' => 'Paid', 'payment_method' => 'Online Transfer', 'total_amount' => '4,200', 'currency' => 'AED', 'guest_notes' => 'Need an extra bed for child.', 'internal_notes' => 'Extra bed setup confirmed.', 'last_updated' => '2023-11-16'],
            3 => ['id' => 3, 'reservation_number' => 'RES-2023-11003', 'guest_name' => 'Ahmed Al Mansoori', 'email' => 'ahmed.m@company.ae', 'phone' => '+971 55 987 6543', 'nationality' => 'UAE', 'hotel' => 'Coral Dubai Deira Hotel', 'room_type' => 'Standard Room', 'check_in_date' => '2023-11-22', 'check_out_date' => '2023-11-24', 'number_of_nights' => 2, 'number_of_adults' => 1, 'number_of_children' => 0, 'booking_source' => 'Corporate', 'reservation_status' => 'Pending', 'payment_status' => 'Pending', 'payment_method' => 'Invoice', 'total_amount' => '1,100', 'currency' => 'AED', 'guest_notes' => 'Late check-in around 11 PM.', 'internal_notes' => 'Corporate account #4459.', 'last_updated' => '2023-11-14'],
            4 => ['id' => 4, 'reservation_number' => 'RES-2023-11004', 'guest_name' => 'Maria Garcia', 'email' => 'mgarcia@example.es', 'phone' => '+34 600 123 456', 'nationality' => 'Spain', 'hotel' => 'ECOS Dubai Hotel', 'room_type' => 'Premium Room', 'check_in_date' => '2023-11-10', 'check_out_date' => '2023-11-15', 'number_of_nights' => 5, 'number_of_adults' => 2, 'number_of_children' => 2, 'booking_source' => 'Expedia', 'reservation_status' => 'Checked Out', 'payment_status' => 'Paid', 'payment_method' => 'Credit Card', 'total_amount' => '2,800', 'currency' => 'AED', 'guest_notes' => 'Connecting rooms if possible.', 'internal_notes' => 'Rooms 401 and 402 provided.', 'last_updated' => '2023-11-15'],
            5 => ['id' => 5, 'reservation_number' => 'RES-2023-11005', 'guest_name' => 'David Wilson', 'email' => 'david.w@techcorp.com', 'phone' => '+1 555 0198', 'nationality' => 'USA', 'hotel' => 'EWA Hotel Apartments', 'room_type' => 'Two Bedroom Apartment', 'check_in_date' => '2023-12-01', 'check_out_date' => '2023-12-15', 'number_of_nights' => 14, 'number_of_adults' => 4, 'number_of_children' => 0, 'booking_source' => 'TravelClick', 'reservation_status' => 'Confirmed', 'payment_status' => 'Paid', 'payment_method' => 'Credit Card', 'total_amount' => '12,500', 'currency' => 'AED', 'guest_notes' => 'Airport pickup required.', 'internal_notes' => 'Transportation arranged for flight EK202.', 'last_updated' => '2023-11-13'],
            6 => ['id' => 6, 'reservation_number' => 'RES-2023-11006', 'guest_name' => 'Fatima Noor', 'email' => 'f.noor@example.com', 'phone' => '+966 50 111 2222', 'nationality' => 'Saudi Arabia', 'hotel' => 'Corp Amman Hotel', 'room_type' => 'Junior Suite', 'check_in_date' => '2023-11-18', 'check_out_date' => '2023-11-20', 'number_of_nights' => 2, 'number_of_adults' => 2, 'number_of_children' => 0, 'booking_source' => 'Agoda', 'reservation_status' => 'Confirmed', 'payment_status' => 'Pending', 'payment_method' => 'Pay at Hotel', 'total_amount' => '300', 'currency' => 'JOD', 'guest_notes' => 'Quiet room preferred.', 'internal_notes' => 'Assigned room at end of hallway.', 'last_updated' => '2023-11-15'],
            7 => ['id' => 7, 'reservation_number' => 'RES-2023-11007', 'guest_name' => 'Thomas Muller', 'email' => 't.muller@example.de', 'phone' => '+49 151 1234567', 'nationality' => 'Germany', 'hotel' => 'Coral Beach Resort Sharjah', 'room_type' => 'Standard Room', 'check_in_date' => '2023-12-24', 'check_out_date' => '2023-12-28', 'number_of_nights' => 4, 'number_of_adults' => 2, 'number_of_children' => 0, 'booking_source' => 'Website', 'reservation_status' => 'Cancel Pending', 'payment_status' => 'Paid', 'payment_method' => 'Credit Card', 'total_amount' => '3,200', 'currency' => 'AED', 'guest_notes' => '', 'internal_notes' => 'Guest called to modify dates, pending confirmation.', 'last_updated' => '2023-11-16'],
            8 => ['id' => 8, 'reservation_number' => 'RES-2023-11008', 'guest_name' => 'Elena Popova', 'email' => 'elena.p@example.ru', 'phone' => '+7 916 123-45-67', 'nationality' => 'Russia', 'hotel' => 'Coral Beach Resort Sharjah', 'room_type' => 'Family Suite', 'check_in_date' => '2023-11-10', 'check_out_date' => '2023-11-20', 'number_of_nights' => 10, 'number_of_adults' => 2, 'number_of_children' => 2, 'booking_source' => 'Expedia', 'reservation_status' => 'Cancelled', 'payment_status' => 'Refunded', 'payment_method' => 'Credit Card', 'total_amount' => '8,500', 'currency' => 'AED', 'guest_notes' => '', 'internal_notes' => 'Cancelled due to visa issues.', 'last_updated' => '2023-11-05'],
            9 => ['id' => 9, 'reservation_number' => 'RES-2023-11009', 'guest_name' => 'James Clarke', 'email' => 'j.clarke@example.com', 'phone' => '+61 400 123 456', 'nationality' => 'Australia', 'hotel' => 'Bahi Ajman Palace Hotel', 'room_type' => 'Deluxe Room', 'check_in_date' => '2023-11-16', 'check_out_date' => '2023-11-21', 'number_of_nights' => 5, 'number_of_adults' => 1, 'number_of_children' => 0, 'booking_source' => 'Booking.com', 'reservation_status' => 'Checked In', 'payment_status' => 'Paid', 'payment_method' => 'Credit Card', 'total_amount' => '2,900', 'currency' => 'AED', 'guest_notes' => '', 'internal_notes' => '', 'last_updated' => '2023-11-16'],
            10 => ['id' => 10, 'reservation_number' => 'RES-2023-11010', 'guest_name' => 'Aisha Khan', 'email' => 'akhan@example.com', 'phone' => '+92 300 1234567', 'nationality' => 'Pakistan', 'hotel' => 'Coral Dubai Deira Hotel', 'room_type' => 'Standard Room', 'check_in_date' => '2023-11-25', 'check_out_date' => '2023-11-27', 'number_of_nights' => 2, 'number_of_adults' => 2, 'number_of_children' => 0, 'booking_source' => 'Website', 'reservation_status' => 'Confirmed', 'payment_status' => 'Paid', 'payment_method' => 'Credit Card', 'total_amount' => '1,200', 'currency' => 'AED', 'guest_notes' => 'Early check-in requested.', 'internal_notes' => 'Subject to availability upon arrival.', 'last_updated' => '2023-11-12'],
            11 => ['id' => 11, 'reservation_number' => 'RES-2023-11011', 'guest_name' => 'Robert Taylor', 'email' => 'robert.t@example.com', 'phone' => '+1 202 555 0123', 'nationality' => 'USA', 'hotel' => 'ECOS Dubai Hotel', 'room_type' => 'Standard Room', 'check_in_date' => '2023-11-30', 'check_out_date' => '2023-12-05', 'number_of_nights' => 5, 'number_of_adults' => 1, 'number_of_children' => 0, 'booking_source' => 'Corporate', 'reservation_status' => 'Confirmed', 'payment_status' => 'Pending', 'payment_method' => 'Invoice', 'total_amount' => '2,500', 'currency' => 'AED', 'guest_notes' => '', 'internal_notes' => 'TechCorp annual summit.', 'last_updated' => '2023-11-14'],
            12 => ['id' => 12, 'reservation_number' => 'RES-2023-11012', 'guest_name' => 'Sophie Martin', 'email' => 'smartin@example.fr', 'phone' => '+33 6 12 34 56 78', 'nationality' => 'France', 'hotel' => 'Coral Beach Resort Sharjah', 'room_type' => 'Deluxe Sea View', 'check_in_date' => '2023-12-10', 'check_out_date' => '2023-12-17', 'number_of_nights' => 7, 'number_of_adults' => 2, 'number_of_children' => 0, 'booking_source' => 'TravelClick', 'reservation_status' => 'Confirmed', 'payment_status' => 'Paid', 'payment_method' => 'Credit Card', 'total_amount' => '5,400', 'currency' => 'AED', 'guest_notes' => '', 'internal_notes' => '', 'last_updated' => '2023-11-08'],
            13 => ['id' => 13, 'reservation_number' => 'RES-2023-11013', 'guest_name' => 'Mohammed Ali', 'email' => 'm.ali@example.om', 'phone' => '+968 9123 4567', 'nationality' => 'Oman', 'hotel' => 'EWA Hotel Apartments', 'room_type' => 'One Bedroom Apartment', 'check_in_date' => '2023-11-15', 'check_out_date' => '2023-11-18', 'number_of_nights' => 3, 'number_of_adults' => 2, 'number_of_children' => 1, 'booking_source' => 'Agoda', 'reservation_status' => 'Checked In', 'payment_status' => 'Paid', 'payment_method' => 'Online Transfer', 'total_amount' => '1,800', 'currency' => 'AED', 'guest_notes' => 'Baby cot needed.', 'internal_notes' => 'Baby cot placed in room 204.', 'last_updated' => '2023-11-15'],
            14 => ['id' => 14, 'reservation_number' => 'RES-2023-11014', 'guest_name' => 'Lisa Wong', 'email' => 'lisa.w@example.sg', 'phone' => '+65 9123 4567', 'nationality' => 'Singapore', 'hotel' => 'Corp Amman Hotel', 'room_type' => 'Executive Room', 'check_in_date' => '2023-11-14', 'check_out_date' => '2023-11-16', 'number_of_nights' => 2, 'number_of_adults' => 1, 'number_of_children' => 0, 'booking_source' => 'Website', 'reservation_status' => 'Checked Out', 'payment_status' => 'Paid', 'payment_method' => 'Credit Card', 'total_amount' => '280', 'currency' => 'JOD', 'guest_notes' => '', 'internal_notes' => '', 'last_updated' => '2023-11-16'],
            15 => ['id' => 15, 'reservation_number' => 'RES-2023-11015', 'guest_name' => 'Daniel Kim', 'email' => 'dkim@example.kr', 'phone' => '+82 10 1234 5678', 'nationality' => 'South Korea', 'hotel' => 'Bahi Ajman Palace Hotel', 'room_type' => 'Deluxe Room', 'check_in_date' => '2023-12-05', 'check_out_date' => '2023-12-08', 'number_of_nights' => 3, 'number_of_adults' => 2, 'number_of_children' => 0, 'booking_source' => 'Booking.com', 'reservation_status' => 'Confirmed', 'payment_status' => 'Pending', 'payment_method' => 'Pay at Hotel', 'total_amount' => '2,100', 'currency' => 'AED', 'guest_notes' => '', 'internal_notes' => '', 'last_updated' => '2023-11-10'],
            16 => ['id' => 16, 'reservation_number' => 'RES-2023-11016', 'guest_name' => 'Yusuf Ibrahim', 'email' => 'y.ibrahim@example.com', 'phone' => '+20 10 1234 5678', 'nationality' => 'Egypt', 'hotel' => 'Coral Dubai Deira Hotel', 'room_type' => 'Suite', 'check_in_date' => '2023-11-19', 'check_out_date' => '2023-11-22', 'number_of_nights' => 3, 'number_of_adults' => 2, 'number_of_children' => 2, 'booking_source' => 'Expedia', 'reservation_status' => 'Pending', 'payment_status' => 'Pending', 'payment_method' => 'Pay at Hotel', 'total_amount' => '3,400', 'currency' => 'AED', 'guest_notes' => 'Wheelchair accessible room required.', 'internal_notes' => 'Assign room 102 (accessible).', 'last_updated' => '2023-11-15'],
            17 => ['id' => 17, 'reservation_number' => 'RES-2023-11017', 'guest_name' => 'Nina Patel', 'email' => 'nina.p@example.in', 'phone' => '+91 98 7654 3210', 'nationality' => 'India', 'hotel' => 'ECOS Dubai Hotel', 'room_type' => 'Premium Room', 'check_in_date' => '2023-11-10', 'check_out_date' => '2023-11-12', 'number_of_nights' => 2, 'number_of_adults' => 2, 'number_of_children' => 0, 'booking_source' => 'Agoda', 'reservation_status' => 'Checked Out', 'payment_status' => 'Paid', 'payment_method' => 'Credit Card', 'total_amount' => '1,100', 'currency' => 'AED', 'guest_notes' => '', 'internal_notes' => '', 'last_updated' => '2023-11-12'],
            18 => ['id' => 18, 'reservation_number' => 'RES-2023-11018', 'guest_name' => 'Jean Dupont', 'email' => 'j.dupont@example.fr', 'phone' => '+33 6 98 76 54 32', 'nationality' => 'France', 'hotel' => 'Coral Beach Resort Sharjah', 'room_type' => 'Standard Room', 'check_in_date' => '2023-11-28', 'check_out_date' => '2023-12-05', 'number_of_nights' => 7, 'number_of_adults' => 2, 'number_of_children' => 0, 'booking_source' => 'Website', 'reservation_status' => 'Confirmed', 'payment_status' => 'Paid', 'payment_method' => 'Credit Card', 'total_amount' => '4,500', 'currency' => 'AED', 'guest_notes' => 'Honeymoon setup.', 'internal_notes' => 'Honeymoon amenities ordered.', 'last_updated' => '2023-11-09'],
            19 => ['id' => 19, 'reservation_number' => 'RES-2023-11019', 'guest_name' => 'Oliver Smith', 'email' => 'o.smith@example.co.uk', 'phone' => '+44 7700 900456', 'nationality' => 'United Kingdom', 'hotel' => 'Bahi Ajman Palace Hotel', 'room_type' => 'Deluxe Room', 'check_in_date' => '2023-11-17', 'check_out_date' => '2023-11-24', 'number_of_nights' => 7, 'number_of_adults' => 2, 'number_of_children' => 0, 'booking_source' => 'TravelClick', 'reservation_status' => 'Pending', 'payment_status' => 'Pending', 'payment_method' => 'Pay at Hotel', 'total_amount' => '5,600', 'currency' => 'AED', 'guest_notes' => '', 'internal_notes' => 'Waiting for deposit payment.', 'last_updated' => '2023-11-16'],
            20 => ['id' => 20, 'reservation_number' => 'RES-2023-11020', 'guest_name' => 'Kenji Tanaka', 'email' => 'k.tanaka@example.jp', 'phone' => '+81 90 1234 5678', 'nationality' => 'Japan', 'hotel' => 'Corp Amman Hotel', 'room_type' => 'Standard Room', 'check_in_date' => '2023-11-20', 'check_out_date' => '2023-11-22', 'number_of_nights' => 2, 'number_of_adults' => 1, 'number_of_children' => 0, 'booking_source' => 'Corporate', 'reservation_status' => 'Confirmed', 'payment_status' => 'Pending', 'payment_method' => 'Invoice', 'total_amount' => '220', 'currency' => 'JOD', 'guest_notes' => 'Late checkout needed.', 'internal_notes' => 'Approved late checkout until 2PM.', 'last_updated' => '2023-11-11'],
        ];
    }
}
