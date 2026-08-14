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

    protected function getViewData(): array
    {
        $query = \App\Models\Member::query();

        if ($this->searchQuery) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->searchQuery}%")
                  ->orWhere('last_name', 'like', "%{$this->searchQuery}%")
                  ->orWhere('email', 'like', "%{$this->searchQuery}%")
                  ->orWhere('id', 'like', "%{$this->searchQuery}%");
            });
        }

        if ($this->filterTier) {
            $query->where('loyalty_tier', $this->filterTier);
        }

        if ($this->filterCountry) {
            $query->where('country', $this->filterCountry);
        }

        if ($this->filterDate) {
            $query->whereDate('created_at', $this->filterDate);
        }

        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));

        $members = $query->skip(($currentPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'member_id' => 'MEM-' . str_pad($member->id, 3, '0', STR_PAD_LEFT),
                    'full_name' => $member->first_name . ' ' . $member->last_name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                    'country' => $member->country,
                    'nationality' => 'Unknown',
                    'gender' => 'Unknown',
                    'date_of_birth' => 'Unknown',
                    'membership_number' => 'HMH-' . str_pad($member->id, 9, '0', STR_PAD_LEFT),
                    'membership_tier' => $member->loyalty_tier ?? 'Silver',
                    'registration_date' => $member->created_at?->format('Y-m-d') ?? '',
                    'status' => 'Active', // Mocked as members table doesn't have status
                    'expiry_date' => 'N/A',
                    'email_notifications' => true,
                    'sms_notifications' => true,
                    'marketing_emails' => true,
                ];
            });

        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'members', 'from', 'to');
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
            ->fillForm(function (array $arguments) {
                $member = \App\Models\Member::find($arguments['id']);
                if (!$member) return [];
                return [
                    'full_name' => $member->first_name . ' ' . $member->last_name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                    'membership_number' => 'HMH-' . str_pad($member->id, 9, '0', STR_PAD_LEFT),
                    'membership_tier' => $member->loyalty_tier ?? 'Silver',
                    'registration_date' => $member->created_at?->format('Y-m-d') ?? '',
                    'status' => 'Active',
                    'nationality' => 'Unknown',
                ];
            })
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
            ->fillForm(function (array $arguments) {
                $member = \App\Models\Member::find($arguments['id']);
                if (!$member) return [];
                return [
                    'full_name' => $member->first_name . ' ' . $member->last_name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                    'country' => $member->country,
                    'membership_tier' => $member->loyalty_tier ?? 'Silver',
                    'status' => 'Active',
                ];
            })
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Members\EditMember::getUrl(['record' => $arguments['id'] ?? 0]))
            
            ->url(fn (array $arguments) => \App\Filament\Pages\Members\ViewMember::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $data, array $arguments) {
                $member = \App\Models\Member::find($arguments['id']);
                if ($member) {
                    $parts = explode(' ', $data['full_name'], 2);
                    $member->first_name = $parts[0] ?? '';
                    $member->last_name = $parts[1] ?? '';
                    $member->email = $data['email'];
                    $member->phone = $data['phone'];
                    $member->country = $data['country'];
                    $member->loyalty_tier = $data['membership_tier'];
                    $member->save();
                }
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
            ->action(function (array $arguments) {
                \App\Models\Member::find($arguments['id'])?->delete();
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

    // Mock Data removed
}
