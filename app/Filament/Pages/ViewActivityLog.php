<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use BackedEnum;

class ViewActivityLog extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'system-administration/activity-logs/{record}';

    protected string $view = 'filament.pages.view-activity-log';

    public $record;

    public function mount($record)
    {
        $this->record = $record;
    }

    public function getLogData(): ?array
    {
        $logs = ManageActivityLogs::getMockLogs();
        foreach ($logs as $log) {
            if ($log['id'] == $this->record) {
                return $log;
            }
        }
        return null;
    }

    public function getTitle(): string | Htmlable
    {
        return 'View Activity Log Details';
    }

    public function getHeading(): string | Htmlable | null
    {
        return 'Activity Log Details';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return 'Detailed information about this specific system event.';
    }

    public function getBackUrl(): string
    {
        return ManageActivityLogs::getUrl();
    }
}
