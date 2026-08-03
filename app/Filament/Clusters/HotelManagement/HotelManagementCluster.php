<?php

namespace App\Filament\Clusters\HotelManagement;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Enums\SubNavigationPosition;

class HotelManagementCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Hotel Management';
    protected static ?string $slug = 'hotel-management';
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
