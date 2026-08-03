<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use BackedEnum;

class ManageUsers extends Page
{
    protected string $view = 'filament.pages.manage-users';

    public $searchQuery = '';
    public $filterRole = '';
    public $filterDepartment = '';
    public $filterHotel = '';
    public $filterStatus = '';
    public $selectedRows = [];
    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterRole(): void { $this->currentPage = 1; }
    public function updatedFilterDepartment(): void { $this->currentPage = 1; }
    public function updatedFilterHotel(): void { $this->currentPage = 1; }
    public function updatedFilterStatus(): void { $this->currentPage = 1; }
    public function updatedPerPage(): void { $this->currentPage = 1; }

    public function nextPage(int $lastPage): void
    {
        if ($this->currentPage < $lastPage) {
            $this->currentPage++;
        }
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function gotoPage(int $page): void
    {
        $this->currentPage = $page;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System Administration';
    }

    public static function getNavigationSort(): ?int
    {
        return 11;
    }

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-user-group';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'User Management';
    }

    public function getTitle(): string | Htmlable
    {
        return 'User Management';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'User Management';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Manage administrators and staff who have access to the HMH Hotel Group CMS.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addUser')
                ->label('Add User')
                ->icon('heroicon-o-plus')
                ->modalHeading('Add New User')
                ->modalWidth('5xl')
                ->form($this->getAddUserFormSchema())
                
            ->url(\App\Filament\Pages\Users\CreateUser::getUrl())
            ->action(function (array $data) {
                    Notification::make()
                        ->title('User created successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function viewUserAction(): Action
    {
        return Action::make('viewUser')
            ->modalHeading('View User Details')
            ->modalWidth('5xl')
            ->form($this->getViewUserFormSchema())
            ->fillForm(fn (array $arguments) => $this->getMockUsers()[$arguments['id']] ?? [])
            ->disabledForm()
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public function editUserAction(): Action
    {
        return Action::make('editUser')
            ->modalHeading('Edit User Details')
            ->modalWidth('5xl')
            ->form($this->getAddUserFormSchema()) // Can reuse same schema
            ->fillForm(fn (array $arguments) => $this->getMockUsers()[$arguments['id']] ?? [])
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Users\EditUser::getUrl(['record' => $arguments['id'] ?? 0]))
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Users\ViewUser::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data) {
                Notification::make()
                    ->title('User updated successfully.')
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
                    ->title('Password reset successfully.')
                    ->success()
                    ->send();
            });
    }

    public function suspendUserAction(): Action
    {
        return Action::make('suspendUser')
            ->icon('heroicon-m-pause-circle')
            ->color('warning')
            ->requiresConfirmation()
            ->action(function () {
                Notification::make()
                    ->title('User suspended successfully.')
                    ->success()
                    ->send();
            });
    }

    public function activateUserAction(): Action
    {
        return Action::make('activateUser')
            ->icon('heroicon-m-check-circle')
            ->color('success')
            ->action(function () {
                Notification::make()
                    ->title('User activated successfully.')
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
                    ->title('User deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public function bulkAction(string $action): void
    {
        if (empty($this->selectedRows)) {
            Notification::make()->title('Please select at least one user.')->warning()->send();
            return;
        }

        $message = match ($action) {
            'activate' => 'Users activated successfully.',
            'suspend' => 'Users suspended successfully.',
            'export' => 'Users exported successfully.',
            'delete' => 'Users deleted.',
            default => 'Action completed.'
        };

        Notification::make()->title($message)->success()->send();
        $this->selectedRows = [];
    }

    public static function getAddUserFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                Grid::make(1)->schema([
                    Section::make('Personal Information')
                        ->schema([
                            TextInput::make('full_name')->label('Full Name')->required(),
                            TextInput::make('employee_id')->label('Employee ID')->required(),
                            TextInput::make('email')->label('Email Address')->email()->required(),
                            TextInput::make('phone')->label('Phone Number')->required(),
                            FileUpload::make('profile_photo')->label('Profile Photo')->image()->avatar(),
                        ]),
                    Section::make('Permissions')
                        ->schema([
                            Placeholder::make('role_description')
                                ->label('Assigned Permissions')
                                ->content('Permissions will be applied based on the selected role.'),
                        ]),
                ])->columnSpan(1),
                
                Grid::make(1)->schema([
                    Section::make('Organisation')
                        ->schema([
                            Select::make('department')
                                ->label('Department')
                                ->options([
                                    'Administration' => 'Administration',
                                    'Marketing' => 'Marketing',
                                    'Reservations' => 'Reservations',
                                    'Operations' => 'Operations',
                                    'Finance' => 'Finance',
                                    'Human Resources' => 'Human Resources',
                                ])->required(),
                            Select::make('assigned_hotel')
                                ->label('Assigned Hotel')
                                ->options([
                                    'Global (All Hotels)' => 'Global (All Hotels)',
                                    'Coral Beach Resort Sharjah' => 'Coral Beach Resort Sharjah',
                                    'Bahi Ajman Palace Hotel' => 'Bahi Ajman Palace Hotel',
                                    'Coral Dubai Deira Hotel' => 'Coral Dubai Deira Hotel',
                                    'ECOS Dubai Hotel' => 'ECOS Dubai Hotel',
                                    'EWA Hotel Apartments' => 'EWA Hotel Apartments',
                                    'Corp Amman Hotel' => 'Corp Amman Hotel',
                                ])->required(),
                            Select::make('role')
                                ->label('Role')
                                ->options([
                                    'Super Admin' => 'Super Admin',
                                    'System Admin' => 'System Admin',
                                    'Hotel Manager' => 'Hotel Manager',
                                    'Marketing Manager' => 'Marketing Manager',
                                    'Reservation Manager' => 'Reservation Manager',
                                    'Content Editor' => 'Content Editor',
                                ])->required(),
                        ]),
                    Section::make('Account')
                        ->schema([
                            TextInput::make('username')->label('Username')->required(),
                            TextInput::make('password')->label('Password')->password()->required(),
                            TextInput::make('password_confirmation')->label('Confirm Password')->password()->required(),
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'Active' => 'Active',
                                    'Inactive' => 'Inactive',
                                    'Suspended' => 'Suspended',
                                ])->default('Active')->required(),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    public static function getViewUserFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                Grid::make(1)->schema([
                    Section::make('Profile Information')
                        ->schema([
                            FileUpload::make('profile_photo')->label('Profile Photo')->image()->avatar(),
                            TextInput::make('full_name')->label('Full Name'),
                            TextInput::make('employee_id')->label('Employee ID'),
                            TextInput::make('email')->label('Email'),
                            TextInput::make('phone')->label('Phone'),
                        ]),
                ])->columnSpan(1),
                
                Grid::make(1)->schema([
                    Section::make('Organisation')
                        ->schema([
                            TextInput::make('department')->label('Department'),
                            TextInput::make('assigned_hotel')->label('Assigned Hotel'),
                            TextInput::make('role')->label('Role'),
                        ]),
                    Section::make('Account Information')
                        ->schema([
                            TextInput::make('username')->label('Username'),
                            TextInput::make('last_login')->label('Last Login'),
                            TextInput::make('created_date')->label('Created Date'),
                            TextInput::make('status')->label('Account Status'),
                        ]),
                ])->columnSpan(1),
            ]),
        ];
    }

    public static function getMockUsers(): array
    {
        return [
            1 => ['id' => 1, 'full_name' => 'Michael Chang', 'employee_id' => 'EMP-001', 'email' => 'michael.c@hmhhotelgroup.com', 'phone' => '+971 50 123 4567', 'department' => 'Administration', 'assigned_hotel' => 'Global (All Hotels)', 'role' => 'Super Admin', 'username' => 'mchang_admin', 'last_login' => '2023-11-20 09:15', 'created_date' => '2019-01-10', 'status' => 'Active'],
            2 => ['id' => 2, 'full_name' => 'Sarah Johnson', 'employee_id' => 'EMP-012', 'email' => 's.johnson@hmhhotelgroup.com', 'phone' => '+971 50 987 6543', 'department' => 'Operations', 'assigned_hotel' => 'Coral Beach Resort Sharjah', 'role' => 'Hotel Manager', 'username' => 'sjohnson_cb', 'last_login' => '2023-11-20 08:30', 'created_date' => '2020-03-15', 'status' => 'Active'],
            3 => ['id' => 3, 'full_name' => 'Ahmed Youssef', 'employee_id' => 'EMP-025', 'email' => 'a.youssef@hmhhotelgroup.com', 'phone' => '+971 55 111 2222', 'department' => 'Marketing', 'assigned_hotel' => 'Global (All Hotels)', 'role' => 'Marketing Manager', 'username' => 'ayoussef_mktg', 'last_login' => '2023-11-19 14:45', 'created_date' => '2021-06-20', 'status' => 'Active'],
            4 => ['id' => 4, 'full_name' => 'Emily Davies', 'employee_id' => 'EMP-038', 'email' => 'e.davies@hmhhotelgroup.com', 'phone' => '+971 56 333 4444', 'department' => 'Reservations', 'assigned_hotel' => 'Global (All Hotels)', 'role' => 'Reservation Manager', 'username' => 'edavies_res', 'last_login' => '2023-11-20 10:05', 'created_date' => '2019-08-12', 'status' => 'Active'],
            5 => ['id' => 5, 'full_name' => 'Tariq Hassan', 'employee_id' => 'EMP-045', 'email' => 't.hassan@hmhhotelgroup.com', 'phone' => '+971 52 555 6666', 'department' => 'Administration', 'assigned_hotel' => 'Global (All Hotels)', 'role' => 'System Admin', 'username' => 'thassan_sys', 'last_login' => '2023-11-18 16:20', 'created_date' => '2022-02-05', 'status' => 'Active'],
            6 => ['id' => 6, 'full_name' => 'Jessica Wong', 'employee_id' => 'EMP-052', 'email' => 'j.wong@hmhhotelgroup.com', 'phone' => '+971 54 777 8888', 'department' => 'Marketing', 'assigned_hotel' => 'Global (All Hotels)', 'role' => 'Content Editor', 'username' => 'jwong_cnt', 'last_login' => '2023-11-15 11:30', 'created_date' => '2022-11-10', 'status' => 'Inactive'],
            7 => ['id' => 7, 'full_name' => 'Omar Farooq', 'employee_id' => 'EMP-061', 'email' => 'o.farooq@hmhhotelgroup.com', 'phone' => '+971 50 999 0000', 'department' => 'Operations', 'assigned_hotel' => 'Bahi Ajman Palace Hotel', 'role' => 'Hotel Manager', 'username' => 'ofarooq_bap', 'last_login' => '2023-11-20 07:45', 'created_date' => '2018-05-25', 'status' => 'Active'],
            8 => ['id' => 8, 'full_name' => 'Nadia Ali', 'employee_id' => 'EMP-074', 'email' => 'n.ali@hmhhotelgroup.com', 'phone' => '+971 55 222 3333', 'department' => 'Human Resources', 'assigned_hotel' => 'Global (All Hotels)', 'role' => 'System Admin', 'username' => 'nali_hr', 'last_login' => '2023-10-30 09:00', 'created_date' => '2020-10-18', 'status' => 'Suspended'],
            9 => ['id' => 9, 'full_name' => 'Robert Taylor', 'employee_id' => 'EMP-088', 'email' => 'r.taylor@hmhhotelgroup.com', 'phone' => '+971 56 444 5555', 'department' => 'Finance', 'assigned_hotel' => 'Global (All Hotels)', 'role' => 'Super Admin', 'username' => 'rtaylor_fin', 'last_login' => '2023-11-19 15:10', 'created_date' => '2017-09-01', 'status' => 'Active'],
            10 => ['id' => 10, 'full_name' => 'Fatima Noor', 'employee_id' => 'EMP-095', 'email' => 'f.noor@hmhhotelgroup.com', 'phone' => '+971 52 666 7777', 'department' => 'Reservations', 'assigned_hotel' => 'Coral Dubai Deira Hotel', 'role' => 'Reservation Manager', 'username' => 'fnoor_cdd', 'last_login' => '2023-11-20 11:25', 'created_date' => '2021-04-14', 'status' => 'Active'],
            11 => ['id' => 11, 'full_name' => 'David Martinez', 'employee_id' => 'EMP-102', 'email' => 'd.martinez@hmhhotelgroup.com', 'phone' => '+971 54 888 9999', 'department' => 'Operations', 'assigned_hotel' => 'ECOS Dubai Hotel', 'role' => 'Hotel Manager', 'username' => 'dmartinez_ecos', 'last_login' => '2023-11-20 08:00', 'created_date' => '2022-07-30', 'status' => 'Active'],
            12 => ['id' => 12, 'full_name' => 'Aisha Rahman', 'employee_id' => 'EMP-115', 'email' => 'a.rahman@hmhhotelgroup.com', 'phone' => '+971 50 111 3333', 'department' => 'Marketing', 'assigned_hotel' => 'Global (All Hotels)', 'role' => 'Content Editor', 'username' => 'arahman_cnt', 'last_login' => '2023-11-17 10:40', 'created_date' => '2023-01-22', 'status' => 'Active'],
            13 => ['id' => 13, 'full_name' => 'James Wilson', 'employee_id' => 'EMP-128', 'email' => 'j.wilson@hmhhotelgroup.com', 'phone' => '+971 55 444 6666', 'department' => 'Reservations', 'assigned_hotel' => 'EWA Hotel Apartments', 'role' => 'Reservation Manager', 'username' => 'jwilson_ewa', 'last_login' => '2023-11-10 13:15', 'created_date' => '2020-12-05', 'status' => 'Inactive'],
            14 => ['id' => 14, 'full_name' => 'Khalid Ibrahim', 'employee_id' => 'EMP-135', 'email' => 'k.ibrahim@hmhhotelgroup.com', 'phone' => '+971 56 777 9999', 'department' => 'Operations', 'assigned_hotel' => 'Corp Amman Hotel', 'role' => 'Hotel Manager', 'username' => 'kibrahim_cam', 'last_login' => '2023-11-20 09:45', 'created_date' => '2019-11-18', 'status' => 'Active'],
            15 => ['id' => 15, 'full_name' => 'Maria Gonzalez', 'employee_id' => 'EMP-142', 'email' => 'm.gonzalez@hmhhotelgroup.com', 'phone' => '+971 52 222 4444', 'department' => 'Administration', 'assigned_hotel' => 'Global (All Hotels)', 'role' => 'System Admin', 'username' => 'mgonzalez_sys', 'last_login' => '2023-11-01 16:50', 'created_date' => '2023-05-10', 'status' => 'Suspended'],
        ];
    }
}
