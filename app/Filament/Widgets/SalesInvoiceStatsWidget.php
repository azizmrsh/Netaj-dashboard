<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\SalesInvoice;

class SalesInvoiceStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Sales Invoices', SalesInvoice::count())
                ->description('All sales invoices in the system')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
                
            Stat::make('This Month\'s Sales', SalesInvoice::whereMonth('created_at', now()->month)->count())
                ->description('Sales invoices created this month')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
                
            Stat::make('Total Sales Value', '$' . number_format(SalesInvoice::sum('total_amount'), 2))
                ->description('Total value of all sales')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
                
            Stat::make('Average Invoice Value', '$' . number_format(SalesInvoice::avg('total_amount') ?? 0, 2))
                ->description('Average sales invoice amount')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}
