<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Customer;

class CustomerStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Customer::count())
                ->description('All customers in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            Stat::make('Active Customers', Customer::where('is_active', 1)->count())
                ->description('Currently active customers')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Customers with Companies', Customer::whereNotNull('name_company')->count())
                ->description('Customers with company names')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),
                
            Stat::make('Customers with Email', Customer::whereNotNull('email')->count())
                ->description('Customers with email addresses')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('warning'),
        ];
    }
}
