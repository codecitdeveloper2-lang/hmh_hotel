<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use BackedEnum;

class ManageMembers extends Page
{
    protected string $view = 'filament.pages.manage-members';

    public $searchQuery = '';
    public $filterTier = '';
    public $filterStatus = '';
    public $filterCountry = '';
    public $filterDate = '';
    public $selectedRows = [];

    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterTier(): void { $this->currentPage = 1; }
    public function updatedFilterStatus(): void { $this->currentPage = 1; }
    public function updatedFilterCountry(): void { $this->currentPage = 1; }
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
        return 9;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-users';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Members';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Members Management';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Members Management';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage registered members of the HMH Hotel Group membership programme.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportMembers')
                ->label('Export Members')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    Notification::make()
                        ->title('Export completed.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewMemberAction(): Action
    {
        return Action::make('viewMember')
            ->modalHeading('View Member')
            ->modalWidth('7xl')
            ->form($this->getViewMemberFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockMembers()[$arguments['id']] ?? [])
            ->disabledForm()
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public function editMemberAction(): Action
    {
        return Action::make('editMember')
            ->modalHeading('Edit Member')
            ->modalWidth('7xl')
            ->form($this->getEditMemberFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockMembers()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Members\EditMember::getUrl(['record' => $arguments['id'] ?? 0]))
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Members\ViewMember::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('Member updated successfully.')
                    ->success()
                    ->send();
            });
    }

    public function suspendMemberAction(): Action
    {
        return Action::make('suspendMember')
            ->icon('heroicon-m-pause-circle')
            ->color('warning')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('Membership suspended.')
                    ->success()
                    ->send();
            });
    }

    public function activateMemberAction(): Action
    {
        return Action::make('activateMember')
            ->icon('heroicon-m-check-circle')
            ->color('success')
            ->action(function () {
                Notification::make()
                    ->title('Membership activated.')
                    ->success()
                    ->send();
            });
    }

    public function resetPasswordAction(): Action
    {
        return Action::make('resetPassword')
            ->icon('heroicon-m-key')
            ->action(function () {
                Notification::make()
                    ->title('Password reset email sent.')
                    ->success()
                    ->send();
            });
    }

    public function sendEmailAction(): Action
    {
        return Action::make('sendEmail')
            ->icon('heroicon-m-envelope')
            ->form([
                TextInput::make('subject')->label('Subject')->required(),
                \Filament\Forms\Components\Textarea::make('message')->label('Message')->required(),
            ])
            ->action(function () {
                Notification::make()
                    ->title('Email sent successfully.')
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
                    ->title('Member deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public function bulkAction(string $action): void
    {
        if (empty($this->selectedRows)) {
            Notification::make()->title('Please select at least one member.')->warning()->send();
            return;
        }

        $message = match ($action) {
            'activate' => 'Members activated successfully.',
            'suspend' => 'Memberships suspended.',
            'export' => 'Export completed.',
            'delete' => 'Members deleted.',
            default => 'Action completed.'
        };

        Notification::make()->title($message)->success()->send();
        $this->selectedRows = [];
    }

    public static function getViewMemberFormSchema(): array
    {
        return [
            Grid::make(3)->schema([
                Grid::make(1)->schema([
                    Section::make('Personal Information')
                        ->schema([
                            Grid::make(2)->schema([
                                TextInput::make('full_name')->label('Full Name'),
                                FileUpload::make('profile_photo')->label('Profile Photo')->image()->avatar(),
                                TextInput::make('email')->label('Email Address'),
                                TextInput::make('phone')->label('Phone Number'),
                                TextInput::make('date_of_birth')->label('Date of Birth'),
                                TextInput::make('gender')->label('Gender'),
                                TextInput::make('nationality')->label('Nationality'),
                            ]),
                        ]),
                ])->columnSpan(2),
                
                Grid::make(1)->schema([
                    Section::make('Membership Information')
                        ->schema([
                            TextInput::make('membership_number')->label('Membership Number'),
                            TextInput::make('membership_tier')->label('Membership Tier'),
                            TextInput::make('registration_date')->label('Registration Date'),
                            TextInput::make('status')->label('Membership Status'),
                            TextInput::make('expiry_date')->label('Expiry Date'),
                        ]),
                    Section::make('Communication Preferences')
                        ->schema([
                            Toggle::make('email_notifications')->label('Email Notifications')->default(true),
                            Toggle::make('sms_notifications')->label('SMS Notifications')->default(true),
                            Toggle::make('marketing_emails')->label('Marketing Emails')->default(false),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    public static function getEditMemberFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('full_name')->label('Full Name')->required(),
                        TextInput::make('email')->label('Email Address')->email()->required(),
                        TextInput::make('phone')->label('Phone Number')->required(),
                        TextInput::make('country')->label('Country')->required(),
                        DatePicker::make('date_of_birth')->label('Date of Birth'),
                    ])->columnSpan(1),
                
                Section::make('Membership Details')
                    ->schema([
                        Select::make('membership_tier')
                            ->label('Membership Tier')
                            ->options([
                                'Silver' => 'Silver',
                                'Gold' => 'Gold',
                                'Platinum' => 'Platinum',
                                'Corporate' => 'Corporate',
                            ])->required(),
                        Select::make('status')
                            ->label('Membership Status')
                            ->options([
                                'Active' => 'Active',
                                'Inactive' => 'Inactive',
                                'Suspended' => 'Suspended',
                            ])->required(),
                        DatePicker::make('expiry_date')->label('Membership Expiry Date'),
                    ])->columnSpan(1),
            ]),
        ];
    }

    public static function getMockMembers(): array
    {
        return [
            1 => ['id' => 1, 'member_id' => 'MEM-001', 'full_name' => 'John Smith', 'email' => 'john.smith@example.com', 'phone' => '+971 50 123 4567', 'country' => 'UAE', 'nationality' => 'Emirati', 'gender' => 'Male', 'date_of_birth' => '1985-06-15', 'membership_number' => 'HMH-987654321', 'membership_tier' => 'Platinum', 'registration_date' => '2020-01-15', 'status' => 'Active', 'expiry_date' => '2024-12-31', 'email_notifications' => true, 'sms_notifications' => true, 'marketing_emails' => true],
            2 => ['id' => 2, 'member_id' => 'MEM-002', 'full_name' => 'Sarah Jenkins', 'email' => 's.jenkins@example.co.uk', 'phone' => '+44 7700 900123', 'country' => 'United Kingdom', 'nationality' => 'British', 'gender' => 'Female', 'date_of_birth' => '1990-03-22', 'membership_number' => 'HMH-876543210', 'membership_tier' => 'Gold', 'registration_date' => '2021-05-20', 'status' => 'Active', 'expiry_date' => '2024-05-20', 'email_notifications' => true, 'sms_notifications' => false, 'marketing_emails' => true],
            3 => ['id' => 3, 'member_id' => 'MEM-003', 'full_name' => 'Ahmed Al Mansoori', 'email' => 'ahmed.m@company.ae', 'phone' => '+971 55 987 6543', 'country' => 'UAE', 'nationality' => 'Emirati', 'gender' => 'Male', 'date_of_birth' => '1978-11-10', 'membership_number' => 'HMH-765432109', 'membership_tier' => 'Corporate', 'registration_date' => '2019-11-10', 'status' => 'Active', 'expiry_date' => '2025-11-10', 'email_notifications' => true, 'sms_notifications' => true, 'marketing_emails' => false],
            4 => ['id' => 4, 'member_id' => 'MEM-004', 'full_name' => 'Maria Garcia', 'email' => 'mgarcia@example.es', 'phone' => '+34 600 123 456', 'country' => 'Spain', 'nationality' => 'Spanish', 'gender' => 'Female', 'date_of_birth' => '1992-08-05', 'membership_number' => 'HMH-654321098', 'membership_tier' => 'Silver', 'registration_date' => '2023-02-14', 'status' => 'Inactive', 'expiry_date' => '2024-02-14', 'email_notifications' => false, 'sms_notifications' => false, 'marketing_emails' => false],
            5 => ['id' => 5, 'member_id' => 'MEM-005', 'full_name' => 'David Wilson', 'email' => 'david.w@techcorp.com', 'phone' => '+1 555 0198', 'country' => 'USA', 'nationality' => 'American', 'gender' => 'Male', 'date_of_birth' => '1982-01-30', 'membership_number' => 'HMH-543210987', 'membership_tier' => 'Corporate', 'registration_date' => '2022-09-01', 'status' => 'Active', 'expiry_date' => '2024-09-01', 'email_notifications' => true, 'sms_notifications' => true, 'marketing_emails' => true],
            6 => ['id' => 6, 'member_id' => 'MEM-006', 'full_name' => 'Fatima Noor', 'email' => 'f.noor@example.com', 'phone' => '+966 50 111 2222', 'country' => 'Saudi Arabia', 'nationality' => 'Saudi', 'gender' => 'Female', 'date_of_birth' => '1988-12-12', 'membership_number' => 'HMH-432109876', 'membership_tier' => 'Gold', 'registration_date' => '2021-08-15', 'status' => 'Active', 'expiry_date' => '2024-08-15', 'email_notifications' => true, 'sms_notifications' => true, 'marketing_emails' => false],
            7 => ['id' => 7, 'member_id' => 'MEM-007', 'full_name' => 'Thomas Muller', 'email' => 't.muller@example.de', 'phone' => '+49 151 1234567', 'country' => 'Germany', 'nationality' => 'German', 'gender' => 'Male', 'date_of_birth' => '1975-04-25', 'membership_number' => 'HMH-321098765', 'membership_tier' => 'Platinum', 'registration_date' => '2018-05-10', 'status' => 'Suspended', 'expiry_date' => '2023-05-10', 'email_notifications' => false, 'sms_notifications' => false, 'marketing_emails' => false],
            8 => ['id' => 8, 'member_id' => 'MEM-008', 'full_name' => 'Elena Popova', 'email' => 'elena.p@example.ru', 'phone' => '+7 916 123-45-67', 'country' => 'Russia', 'nationality' => 'Russian', 'gender' => 'Female', 'date_of_birth' => '1995-09-09', 'membership_number' => 'HMH-210987654', 'membership_tier' => 'Silver', 'registration_date' => '2023-10-01', 'status' => 'Active', 'expiry_date' => '2024-10-01', 'email_notifications' => true, 'sms_notifications' => false, 'marketing_emails' => true],
            9 => ['id' => 9, 'member_id' => 'MEM-009', 'full_name' => 'James Clarke', 'email' => 'j.clarke@example.com', 'phone' => '+61 400 123 456', 'country' => 'Australia', 'nationality' => 'Australian', 'gender' => 'Male', 'date_of_birth' => '1980-02-18', 'membership_number' => 'HMH-109876543', 'membership_tier' => 'Gold', 'registration_date' => '2020-12-05', 'status' => 'Active', 'expiry_date' => '2024-12-05', 'email_notifications' => true, 'sms_notifications' => true, 'marketing_emails' => true],
            10 => ['id' => 10, 'member_id' => 'MEM-010', 'full_name' => 'Aisha Khan', 'email' => 'akhan@example.com', 'phone' => '+92 300 1234567', 'country' => 'Pakistan', 'nationality' => 'Pakistani', 'gender' => 'Female', 'date_of_birth' => '1991-07-30', 'membership_number' => 'HMH-098765432', 'membership_tier' => 'Silver', 'registration_date' => '2023-11-01', 'status' => 'Active', 'expiry_date' => '2024-11-01', 'email_notifications' => true, 'sms_notifications' => false, 'marketing_emails' => true],
            11 => ['id' => 11, 'member_id' => 'MEM-011', 'full_name' => 'Robert Taylor', 'email' => 'robert.t@example.com', 'phone' => '+1 202 555 0123', 'country' => 'USA', 'nationality' => 'American', 'gender' => 'Male', 'date_of_birth' => '1968-10-12', 'membership_number' => 'HMH-987654322', 'membership_tier' => 'Platinum', 'registration_date' => '2017-03-20', 'status' => 'Active', 'expiry_date' => '2025-03-20', 'email_notifications' => true, 'sms_notifications' => true, 'marketing_emails' => true],
            12 => ['id' => 12, 'member_id' => 'MEM-012', 'full_name' => 'Sophie Martin', 'email' => 'smartin@example.fr', 'phone' => '+33 6 12 34 56 78', 'country' => 'France', 'nationality' => 'French', 'gender' => 'Female', 'date_of_birth' => '1987-05-08', 'membership_number' => 'HMH-876543211', 'membership_tier' => 'Gold', 'registration_date' => '2022-01-15', 'status' => 'Active', 'expiry_date' => '2024-01-15', 'email_notifications' => true, 'sms_notifications' => true, 'marketing_emails' => false],
            13 => ['id' => 13, 'member_id' => 'MEM-013', 'full_name' => 'Mohammed Ali', 'email' => 'm.ali@example.om', 'phone' => '+968 9123 4567', 'country' => 'Oman', 'nationality' => 'Omani', 'gender' => 'Male', 'date_of_birth' => '1983-09-25', 'membership_number' => 'HMH-765432108', 'membership_tier' => 'Silver', 'registration_date' => '2023-06-10', 'status' => 'Active', 'expiry_date' => '2024-06-10', 'email_notifications' => true, 'sms_notifications' => false, 'marketing_emails' => true],
            14 => ['id' => 14, 'member_id' => 'MEM-014', 'full_name' => 'Lisa Wong', 'email' => 'lisa.w@example.sg', 'phone' => '+65 9123 4567', 'country' => 'Singapore', 'nationality' => 'Singaporean', 'gender' => 'Female', 'date_of_birth' => '1994-11-18', 'membership_number' => 'HMH-654321097', 'membership_tier' => 'Gold', 'registration_date' => '2021-10-05', 'status' => 'Active', 'expiry_date' => '2024-10-05', 'email_notifications' => true, 'sms_notifications' => true, 'marketing_emails' => true],
            15 => ['id' => 15, 'member_id' => 'MEM-015', 'full_name' => 'Daniel Kim', 'email' => 'dkim@example.kr', 'phone' => '+82 10 1234 5678', 'country' => 'South Korea', 'nationality' => 'Korean', 'gender' => 'Male', 'date_of_birth' => '1989-04-14', 'membership_number' => 'HMH-543210986', 'membership_tier' => 'Silver', 'registration_date' => '2023-08-20', 'status' => 'Inactive', 'expiry_date' => '2024-08-20', 'email_notifications' => false, 'sms_notifications' => false, 'marketing_emails' => false],
            16 => ['id' => 16, 'member_id' => 'MEM-016', 'full_name' => 'Yusuf Ibrahim', 'email' => 'y.ibrahim@example.com', 'phone' => '+20 10 1234 5678', 'country' => 'Egypt', 'nationality' => 'Egyptian', 'gender' => 'Male', 'date_of_birth' => '1986-12-05', 'membership_number' => 'HMH-432109875', 'membership_tier' => 'Gold', 'registration_date' => '2020-07-15', 'status' => 'Active', 'expiry_date' => '2024-07-15', 'email_notifications' => true, 'sms_notifications' => true, 'marketing_emails' => true],
            17 => ['id' => 17, 'member_id' => 'MEM-017', 'full_name' => 'Nina Patel', 'email' => 'nina.p@example.in', 'phone' => '+91 98 7654 3210', 'country' => 'India', 'nationality' => 'Indian', 'gender' => 'Female', 'date_of_birth' => '1993-02-28', 'membership_number' => 'HMH-321098764', 'membership_tier' => 'Silver', 'registration_date' => '2023-09-10', 'status' => 'Suspended', 'expiry_date' => '2024-09-10', 'email_notifications' => false, 'sms_notifications' => false, 'marketing_emails' => false],
            18 => ['id' => 18, 'member_id' => 'MEM-018', 'full_name' => 'Jean Dupont', 'email' => 'j.dupont@example.fr', 'phone' => '+33 6 98 76 54 32', 'country' => 'France', 'nationality' => 'French', 'gender' => 'Male', 'date_of_birth' => '1979-08-15', 'membership_number' => 'HMH-210987653', 'membership_tier' => 'Platinum', 'registration_date' => '2016-11-20', 'status' => 'Active', 'expiry_date' => '2024-11-20', 'email_notifications' => true, 'sms_notifications' => true, 'marketing_emails' => true],
            19 => ['id' => 19, 'member_id' => 'MEM-019', 'full_name' => 'Oliver Smith', 'email' => 'o.smith@example.co.uk', 'phone' => '+44 7700 900456', 'country' => 'United Kingdom', 'nationality' => 'British', 'gender' => 'Male', 'date_of_birth' => '1996-01-10', 'membership_number' => 'HMH-109876542', 'membership_tier' => 'Silver', 'registration_date' => '2023-11-05', 'status' => 'Active', 'expiry_date' => '2024-11-05', 'email_notifications' => true, 'sms_notifications' => false, 'marketing_emails' => true],
            20 => ['id' => 20, 'member_id' => 'MEM-020', 'full_name' => 'Kenji Tanaka', 'email' => 'k.tanaka@example.jp', 'phone' => '+81 90 1234 5678', 'country' => 'Japan', 'nationality' => 'Japanese', 'gender' => 'Male', 'date_of_birth' => '1984-06-30', 'membership_number' => 'HMH-098765431', 'membership_tier' => 'Corporate', 'registration_date' => '2019-04-01', 'status' => 'Active', 'expiry_date' => '2025-04-01', 'email_notifications' => true, 'sms_notifications' => true, 'marketing_emails' => false],
        ];
    }
}
