<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class ReservationsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Reservations (Last 6 Months)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Total Reservations',
                    'data' => [120, 180, 240, 210, 310, 420],
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.2)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
