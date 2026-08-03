<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class RecentActivityWidget extends Widget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    protected string $view = 'filament.widgets.recent-activity-widget';
}
