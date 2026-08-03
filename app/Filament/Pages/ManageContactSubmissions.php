<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use BackedEnum;

class ManageContactSubmissions extends Page
{
    protected string $view = 'filament.pages.manage-contact-submissions';

    public $searchQuery = '';
    public $filterHotel = '';
    public $filterType = '';
    public $filterStatus = '';
    public $filterAssigned = '';
    public $filterDate = '';
    public $selectedRows = [];
    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterHotel(): void { $this->currentPage = 1; }
    public function updatedFilterType(): void { $this->currentPage = 1; }
    public function updatedFilterStatus(): void { $this->currentPage = 1; }
    public function updatedFilterAssigned(): void { $this->currentPage = 1; }
    public function updatedFilterDate(): void { $this->currentPage = 1; }
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
        return 7;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-envelope';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Contact Submissions';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Contact Submissions';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Contact Submissions';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'View, organise and manage customer enquiries received through the HMH Hotel Group website.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    Notification::make()
                        ->title('Export completed.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewSubmissionAction(): Action
    {
        return Action::make('viewSubmission')
            ->modalHeading('View Submission')
            ->modalWidth('7xl')
            ->form($this->getSubmissionFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockSubmissions()[$arguments['id']] ?? [])
            ->disabledForm()
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public function markAsReadAction(): Action
    {
        return Action::make('markAsRead')
            ->icon('heroicon-m-envelope-open')
            
            ->url(fn (array $arguments) => \App\Filament\Pages\ContactSubmissions\ViewContactSubmission::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function () {
                Notification::make()
                    ->title('Enquiry marked as read.')
                    ->success()
                    ->send();
            });
    }

    public function markAsRespondedAction(): Action
    {
        return Action::make('markAsResponded')
            ->icon('heroicon-m-check-circle')
            ->action(function () {
                Notification::make()
                    ->title('Enquiry marked as responded.')
                    ->success()
                    ->send();
            });
    }

    public function assignAction(): Action
    {
        return Action::make('assign')
            ->icon('heroicon-m-user-plus')
            ->form([
                Select::make('assigned_staff')
                    ->label('Select Staff Member')
                    ->options([
                        'Sarah Johnson' => 'Sarah Johnson',
                        'Michael Chen' => 'Michael Chen',
                        'Emma Davis' => 'Emma Davis',
                        'James Wilson' => 'James Wilson',
                    ])
                    ->required()
            ])
            ->action(function () {
                Notification::make()
                    ->title('Staff assigned successfully.')
                    ->success()
                    ->send();
            });
    }

    public function printAction(): Action
    {
        return Action::make('print')
            ->icon('heroicon-m-printer')
            ->action(function () {
                Notification::make()
                    ->title('Printing submission...')
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
                    ->title('Export completed.')
                    ->success()
                    ->send();
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->icon('heroicon-m-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Submission deleted.')
                    ->success()
                    ->send();
            });
    }

    public function bulkAction(string $action): void
    {
        if (empty($this->selectedRows)) {
            Notification::make()->title('Please select at least one record.')->warning()->send();
            return;
        }

        $message = match ($action) {
            'read' => 'Enquiries marked as read.',
            'responded' => 'Enquiries marked as responded.',
            'assign' => 'Staff assigned successfully.',
            'export' => 'Export completed.',
            'delete' => 'Submissions deleted.',
            default => 'Action completed.'
        };

        Notification::make()->title($message)->success()->send();
        $this->selectedRows = [];
    }

    public static function getSubmissionFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Customer Details')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('customer_name')->label('Full Name'),
                                TextInput::make('email')->label('Email'),
                                TextInput::make('phone')->label('Phone Number'),
                                TextInput::make('country')->label('Country'),
                            ]),
                        ]),

                    Section::make('Enquiry Details')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('hotel')->label('Hotel'),
                                TextInput::make('enquiry_type')->label('Enquiry Type'),
                            ]),
                            TextInput::make('subject')->label('Subject'),
                            Textarea::make('message')->label('Message')->rows(6),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('System Details')
                        ->schema([
                            TextInput::make('submission_id')->label('Submission ID'),
                            TextInput::make('submitted_on')->label('Submission Date'),
                            TextInput::make('status')->label('Status'),
                            TextInput::make('assigned_to')->label('Assigned Staff'),
                            Textarea::make('internal_notes')->label('Internal Notes (Read Only)')->rows(4),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    public static function getMockSubmissions(): array
    {
        return [
            1 => ['id' => 1, 'submission_id' => 'SUB-2023-1001', 'customer_name' => 'John Smith', 'email' => 'john.smith@example.com', 'phone' => '+971 50 123 4567', 'country' => 'UAE', 'hotel' => 'Coral Beach Resort Sharjah', 'subject' => 'Booking Inquiry for December', 'enquiry_type' => 'Hotel Booking', 'submitted_on' => '2023-11-15 09:30', 'status' => 'New', 'assigned_to' => 'Unassigned', 'message' => 'I would like to know if you have availability for a family of 4 from Dec 20 to Dec 27.', 'internal_notes' => ''],
            2 => ['id' => 2, 'submission_id' => 'SUB-2023-1002', 'customer_name' => 'Sarah Jenkins', 'email' => 's.jenkins@company.com', 'phone' => '+44 7700 900123', 'country' => 'UK', 'hotel' => 'Bahi Ajman Palace Hotel', 'subject' => 'Corporate Event 2024', 'enquiry_type' => 'Corporate Partnership', 'submitted_on' => '2023-11-14 14:15', 'status' => 'Pending', 'assigned_to' => 'Sarah Johnson', 'message' => 'We are planning our annual corporate retreat and need a proposal for 50 rooms and meeting facilities.', 'internal_notes' => 'Sarah J is preparing the proposal.'],
            3 => ['id' => 3, 'submission_id' => 'SUB-2023-1003', 'customer_name' => 'Ahmed Al Mansoori', 'email' => 'ahmed.m@example.ae', 'phone' => '+971 55 987 6543', 'country' => 'UAE', 'hotel' => 'Coral Dubai Deira Hotel', 'subject' => 'Wedding Reception Availability', 'enquiry_type' => 'Wedding Enquiry', 'submitted_on' => '2023-11-14 10:05', 'status' => 'Responded', 'assigned_to' => 'Emma Davis', 'message' => 'Looking for a ballroom for a wedding reception for 300 guests in February.', 'internal_notes' => 'Sent the wedding brochure.'],
            4 => ['id' => 4, 'submission_id' => 'SUB-2023-1004', 'customer_name' => 'Maria Garcia', 'email' => 'mgarcia@example.es', 'phone' => '+34 600 123 456', 'country' => 'Spain', 'hotel' => 'ECOS Dubai Hotel', 'subject' => 'Late Check-in Request', 'enquiry_type' => 'General Enquiry', 'submitted_on' => '2023-11-13 18:45', 'status' => 'Responded', 'assigned_to' => 'Michael Chen', 'message' => 'My flight arrives at 2 AM. Will someone be at the reception?', 'internal_notes' => 'Confirmed 24/7 reception.'],
            5 => ['id' => 5, 'submission_id' => 'SUB-2023-1005', 'customer_name' => 'David Wilson', 'email' => 'david.w@techcorp.com', 'phone' => '+1 555 0198', 'country' => 'USA', 'hotel' => 'EWA Hotel Apartments', 'subject' => 'Long Term Stay Rates', 'enquiry_type' => 'Group Booking', 'submitted_on' => '2023-11-13 08:20', 'status' => 'New', 'assigned_to' => 'Unassigned', 'message' => 'Need rates for 5 apartments for a 3-month project.', 'internal_notes' => ''],
            6 => ['id' => 6, 'submission_id' => 'SUB-2023-1006', 'customer_name' => 'Fatima Noor', 'email' => 'f.noor@example.com', 'phone' => '+966 50 111 2222', 'country' => 'Saudi Arabia', 'hotel' => 'Corp Amman Hotel', 'subject' => 'Conference Hall Booking', 'enquiry_type' => 'Meetings & Events', 'submitted_on' => '2023-11-12 11:10', 'status' => 'Pending', 'assigned_to' => 'James Wilson', 'message' => 'Requesting a quote for a 2-day conference for 150 people with catering.', 'internal_notes' => 'Checking hall availability.'],
            7 => ['id' => 7, 'submission_id' => 'SUB-2023-1007', 'customer_name' => 'Thomas Muller', 'email' => 't.muller@example.de', 'phone' => '+49 151 1234567', 'country' => 'Germany', 'hotel' => 'All Hotels', 'subject' => 'Loyalty Program Issue', 'enquiry_type' => 'Membership', 'submitted_on' => '2023-11-12 09:15', 'status' => 'New', 'assigned_to' => 'Unassigned', 'message' => 'I cannot log into my membership account to view my points.', 'internal_notes' => ''],
            8 => ['id' => 8, 'submission_id' => 'SUB-2023-1008', 'customer_name' => 'Elena Popova', 'email' => 'elena.p@example.ru', 'phone' => '+7 916 123-45-67', 'country' => 'Russia', 'hotel' => 'Coral Beach Resort Sharjah', 'subject' => 'Great Stay!', 'enquiry_type' => 'Feedback', 'submitted_on' => '2023-11-11 16:30', 'status' => 'Responded', 'assigned_to' => 'Sarah Johnson', 'message' => 'We had a wonderful stay at your resort. The food was excellent.', 'internal_notes' => 'Sent thank you email.'],
            9 => ['id' => 9, 'submission_id' => 'SUB-2023-1009', 'customer_name' => 'James Clarke', 'email' => 'j.clarke@example.com', 'phone' => '+61 400 123 456', 'country' => 'Australia', 'hotel' => 'Coral Dubai Deira Hotel', 'subject' => 'AC Not Working Properly', 'enquiry_type' => 'Complaint', 'submitted_on' => '2023-11-11 10:45', 'status' => 'Pending', 'assigned_to' => 'Michael Chen', 'message' => 'During my stay last week in room 412, the AC was very noisy.', 'internal_notes' => 'Forwarded to maintenance.'],
            10 => ['id' => 10, 'submission_id' => 'SUB-2023-1010', 'customer_name' => 'Aisha Khan', 'email' => 'akhan@example.com', 'phone' => '+92 300 1234567', 'country' => 'Pakistan', 'hotel' => 'Corporate Office', 'subject' => 'Marketing Manager Position', 'enquiry_type' => 'Career Enquiry', 'submitted_on' => '2023-11-10 13:20', 'status' => 'New', 'assigned_to' => 'Unassigned', 'message' => 'I have applied for the Marketing Manager role and wanted to follow up.', 'internal_notes' => ''],
            11 => ['id' => 11, 'submission_id' => 'SUB-2023-1011', 'customer_name' => 'Robert Taylor', 'email' => 'robert.t@example.com', 'phone' => '+1 202 555 0123', 'country' => 'USA', 'hotel' => 'Bahi Ajman Palace Hotel', 'subject' => 'Airport Transfer', 'enquiry_type' => 'General Enquiry', 'submitted_on' => '2023-11-10 08:05', 'status' => 'Responded', 'assigned_to' => 'Emma Davis', 'message' => 'Do you provide shuttle service from Dubai airport?', 'internal_notes' => 'Provided transfer rates.'],
            12 => ['id' => 12, 'submission_id' => 'SUB-2023-1012', 'customer_name' => 'Sophie Martin', 'email' => 'smartin@example.fr', 'phone' => '+33 6 12 34 56 78', 'country' => 'France', 'hotel' => 'ECOS Dubai Hotel', 'subject' => 'Restaurant Reservation', 'enquiry_type' => 'General Enquiry', 'submitted_on' => '2023-11-09 19:30', 'status' => 'New', 'assigned_to' => 'Unassigned', 'message' => 'I want to book a table for 4 at your main restaurant this Friday.', 'internal_notes' => ''],
            13 => ['id' => 13, 'submission_id' => 'SUB-2023-1013', 'customer_name' => 'Mohammed Ali', 'email' => 'm.ali@example.om', 'phone' => '+968 9123 4567', 'country' => 'Oman', 'hotel' => 'EWA Hotel Apartments', 'subject' => 'Payment Issue', 'enquiry_type' => 'Complaint', 'submitted_on' => '2023-11-09 11:15', 'status' => 'Pending', 'assigned_to' => 'James Wilson', 'message' => 'My card was charged twice for the deposit.', 'internal_notes' => 'Finance is investigating.'],
            14 => ['id' => 14, 'submission_id' => 'SUB-2023-1014', 'customer_name' => 'Lisa Wong', 'email' => 'lisa.w@example.sg', 'phone' => '+65 9123 4567', 'country' => 'Singapore', 'hotel' => 'Corp Amman Hotel', 'subject' => 'Spa Packages', 'enquiry_type' => 'General Enquiry', 'submitted_on' => '2023-11-08 14:40', 'status' => 'Responded', 'assigned_to' => 'Sarah Johnson', 'message' => 'Could you send me the latest spa brochure?', 'internal_notes' => 'Sent brochure PDF.'],
            15 => ['id' => 15, 'submission_id' => 'SUB-2023-1015', 'customer_name' => 'Daniel Kim', 'email' => 'dkim@example.kr', 'phone' => '+82 10 1234 5678', 'country' => 'South Korea', 'hotel' => 'Coral Beach Resort Sharjah', 'subject' => 'Early Check-in', 'enquiry_type' => 'Hotel Booking', 'submitted_on' => '2023-11-08 07:25', 'status' => 'New', 'assigned_to' => 'Unassigned', 'message' => 'Is it possible to check in at 10 AM?', 'internal_notes' => ''],
        ];
    }
}
