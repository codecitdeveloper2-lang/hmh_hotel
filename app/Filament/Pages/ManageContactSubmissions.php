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

    protected function getViewData(): array
    {
        $query = \App\Models\ContactSubmission::query();
        
        $totalItems  = $query->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        
        $submissions = $query->skip(($currentPage - 1) * $this->perPage)
                        ->take($this->perPage)
                        ->get()
                        ->map(function ($sub) {
                            return [
                                'id' => $sub->id,
                                'submission_id' => 'SUB-' . $sub->id,
                                'customer_name' => $sub->name,
                                'email' => $sub->email,
                                'phone' => $sub->phone,
                                'country' => 'Unknown',
                                'hotel' => 'Unknown',
                                'subject' => $sub->subject,
                                'enquiry_type' => 'General',
                                'submitted_on' => $sub->created_at?->format('Y-m-d H:i') ?? '',
                                'status' => ucfirst($sub->status),
                                'assigned_to' => 'Unassigned',
                                'message' => $sub->message,
                                'internal_notes' => '',
                            ];
                        });
                        
        $from = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to   = min($currentPage * $this->perPage, $totalItems);

        return compact('totalItems', 'lastPage', 'currentPage', 'submissions', 'from', 'to');
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
            ->fillForm(function (array $arguments) {
                $sub = \App\Models\ContactSubmission::find($arguments['id']);
                if (!$sub) return [];
                return [
                    'submission_id' => 'SUB-' . $sub->id,
                    'customer_name' => $sub->name,
                    'email' => $sub->email,
                    'phone' => $sub->phone,
                    'subject' => $sub->subject,
                    'message' => $sub->message,
                    'status' => ucfirst($sub->status),
                    'submitted_on' => $sub->created_at,
                ];
            })
            ->disabledForm()
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close');
    }

    public function markAsReadAction(): Action
    {
        return Action::make('markAsRead')
            ->icon('heroicon-m-envelope-open')
            
            ->url(fn (array $arguments) => \App\Filament\Pages\ContactSubmissions\ViewContactSubmission::getUrl(['record' => $arguments['id'] ?? 0]))
            ->action(function (array $arguments) {
                \App\Models\ContactSubmission::find($arguments['id'])?->update(['status' => 'read']);
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
            ->action(function (array $arguments) {
                \App\Models\ContactSubmission::find($arguments['id'])?->update(['status' => 'replied']);
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
            ->action(function (array $arguments) {
                \App\Models\ContactSubmission::find($arguments['id'])?->delete();
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

    // Mock Data removed
}
