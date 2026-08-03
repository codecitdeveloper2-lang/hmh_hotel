<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class OccupancyChartWidget extends ChartWidget
{
    protected ?string $heading = 'Occupancy by Hotel (Current)';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Occupancy Rate (%)',
                    'data' => [85, 92, 78, 65, 88],
                    'backgroundColor' => [
                        '#6366f1', // Indigo
                        '#10b981', // Emerald
                        '#f59e0b', // Amber
                        '#ef4444', // Red
                        '#8b5cf6', // Violet
                    ],
                    'borderWidth' => 0,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['Coral Beach', 'Bahi Ajman', 'ECOS Dubai', 'EWA Hotel', 'Corp Amman'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
