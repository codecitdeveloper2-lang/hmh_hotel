<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class ManageActivityLogs extends Page
{
    public static function getNavigationGroup(): ?string
    {
        return 'System Administration';
    }
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'system-administration/activity-logs';

    protected string $view = 'filament.pages.manage-activity-logs';

    public $searchQuery = '';
    public $filterUser = '';
    public $filterModule = '';
    public $filterAction = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';
    
    public int $perPage = 10;
    public int $currentPage = 1;

    public function updatedSearchQuery(): void { $this->currentPage = 1; }
    public function updatedFilterUser(): void { $this->currentPage = 1; }
    public function updatedFilterModule(): void { $this->currentPage = 1; }
    public function updatedFilterAction(): void { $this->currentPage = 1; }
    public function updatedFilterDateFrom(): void { $this->currentPage = 1; }
    public function updatedFilterDateTo(): void { $this->currentPage = 1; }
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

    public static function getNavigationIcon(): string | BackedEnum | Htmlable | null
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public static function getNavigationLabel(): string
    {
        return 'Activity Logs';
    }

    public function getTitle(): string | Htmlable
    {
        return 'Activity Logs';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Activity Logs';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Monitor system events, user actions, and security logs.';
    }

    protected function getViewData(): array
    {
        $all = collect($this->getMockLogs())
            ->when($this->searchQuery, function ($collection) {
                return $collection->filter(function ($item) {
                    $query = strtolower($this->searchQuery);
                    return str_contains(strtolower($item['user_name']), $query) ||
                           str_contains(strtolower($item['record_name']), $query) ||
                           str_contains(strtolower($item['action']), $query);
                });
            })
            ->when($this->filterUser, fn($collection) => $collection->where('user_name', $this->filterUser))
            ->when($this->filterModule, fn($collection) => $collection->where('module', $this->filterModule))
            ->when($this->filterAction, fn($collection) => $collection->where('action', $this->filterAction))
            ->when($this->filterDateFrom, fn($collection) => $collection->where('date_time', '>=', $this->filterDateFrom . ' 00:00:00'))
            ->when($this->filterDateTo, fn($collection) => $collection->where('date_time', '<=', $this->filterDateTo . ' 23:59:59'));

        $totalItems  = $all->count();
        $lastPage    = max(1, (int) ceil($totalItems / $this->perPage));
        $currentPage = max(1, min($this->currentPage, $lastPage));
        $logs        = $all->forPage($currentPage, $this->perPage);
        $from        = $totalItems > 0 ? ($currentPage - 1) * $this->perPage + 1 : 0;
        $to          = min($currentPage * $this->perPage, $totalItems);
        
        $totalActivities = collect($this->getMockLogs())->count();
        $todayActivities = collect($this->getMockLogs())->filter(fn($log) => str_contains($log['date_time'], date('Y-m-d')))->count();
        $successfulActions = collect($this->getMockLogs())->where('status', 'Success')->count();
        $failedActions = collect($this->getMockLogs())->where('status', 'Failed')->count();

        return compact(
            'totalItems', 'lastPage', 'currentPage', 'logs', 'from', 'to',
            'totalActivities', 'todayActivities', 'successfulActions', 'failedActions'
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportLogs')
                ->label('Export Logs')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    \Filament\Notifications\Notification::make()
                        ->title('Logs Exported')
                        ->body('Activity logs have been exported successfully.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public static function getMockLogs(): array
    {
        return [
            [
                'id' => 1,
                'user_name' => 'John Doe',
                'role' => 'Administrator',
                'module' => 'Hotel Management',
                'action' => 'Updated',
                'record_name' => 'Coral Beach Resort Sharjah',
                'ip_address' => '192.168.1.10',
                'date_time' => date('Y-m-d') . ' 10:30:00',
                'status' => 'Success',
                'old_value' => 'Status: Inactive',
                'new_value' => 'Status: Active',
                'browser' => 'Chrome 118',
                'device' => 'Windows 11',
            ],
            [
                'id' => 2,
                'user_name' => 'Jane Smith',
                'role' => 'Content Manager',
                'module' => 'Gallery',
                'action' => 'Created',
                'record_name' => 'Main Entrance Photo',
                'ip_address' => '10.0.0.5',
                'date_time' => date('Y-m-d', strtotime('-1 day')) . ' 14:15:00',
                'status' => 'Success',
                'old_value' => 'N/A',
                'new_value' => 'File: main_entrance.jpg uploaded',
                'browser' => 'Safari 16',
                'device' => 'macOS Ventura',
            ],
            [
                'id' => 3,
                'user_name' => 'System',
                'role' => 'System',
                'module' => 'Authentication',
                'action' => 'Failed Login',
                'record_name' => 'admin@hmh.com',
                'ip_address' => '203.0.113.45',
                'date_time' => date('Y-m-d') . ' 08:45:00',
                'status' => 'Failed',
                'old_value' => 'N/A',
                'new_value' => 'Invalid password attempt',
                'browser' => 'Firefox 115',
                'device' => 'Ubuntu Linux',
            ],
            [
                'id' => 4,
                'user_name' => 'Alice Johnson',
                'role' => 'Marketing',
                'module' => 'Offers',
                'action' => 'Deleted',
                'record_name' => 'Summer Discount 2023',
                'ip_address' => '172.16.254.1',
                'date_time' => date('Y-m-d', strtotime('-2 days')) . ' 16:20:00',
                'status' => 'Success',
                'old_value' => 'Offer details...',
                'new_value' => 'N/A',
                'browser' => 'Edge 117',
                'device' => 'Windows 10',
            ],
            [
                'id' => 5,
                'user_name' => 'Bob Williams',
                'role' => 'Reservation Agent',
                'module' => 'Reservations',
                'action' => 'Updated',
                'record_name' => 'Booking #RES-1042',
                'ip_address' => '192.168.1.55',
                'date_time' => date('Y-m-d') . ' 11:10:00',
                'status' => 'Success',
                'old_value' => 'Status: Pending',
                'new_value' => 'Status: Confirmed',
                'browser' => 'Chrome 118',
                'device' => 'macOS Sonoma',
            ],
        ];
    }
}
