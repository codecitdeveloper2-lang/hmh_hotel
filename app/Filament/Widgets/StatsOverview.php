<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Brands', '5')
                ->description('1 new brand added')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([1, 2, 2, 3, 4, 4, 5])
                ->color('success'),
            Stat::make('Total Hotels', '25')
                ->description('3 properties in development')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->chart([15, 18, 20, 20, 22, 24, 25])
                ->color('primary'),
            Stat::make('Total Offers', '18')
                ->description('7% decrease in active offers')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([25, 24, 22, 20, 19, 19, 18])
                ->color('danger'),
            Stat::make('Total Reservations', '1,250')
                ->description('12% increase last 30 days')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([1000, 1050, 1100, 1080, 1150, 1200, 1250])
                ->color('success'),
            Stat::make('Contact Enquiries', '42')
                ->description('Requires attention')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->chart([10, 15, 12, 25, 30, 38, 42])
                ->color('warning'),
            Stat::make('Newsletter Subscribers', '3,580')
                ->description('Steady growth')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([3000, 3100, 3250, 3300, 3450, 3500, 3580])
                ->color('info'),
        ];
    }
}
