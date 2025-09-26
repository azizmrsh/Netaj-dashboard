<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Supplier;

class SupplierStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Suppliers', Supplier::count())
                ->description('All suppliers in the system')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            Stat::make('Active Suppliers', Supplier::where('is_active', 1)->count())
                ->description('Currently active suppliers')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Suppliers with Companies', Supplier::whereNotNull('name_company')->count())
                ->description('Suppliers with company names')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),
                
            Stat::make('Suppliers with Tax Number', Supplier::whereNotNull('tax_number')->count())
                ->description('Suppliers with tax numbers')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('warning'),
        ];
    }
}
