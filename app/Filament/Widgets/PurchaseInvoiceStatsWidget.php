<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\PurchaseInvoice;

class PurchaseInvoiceStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Purchase Invoices', PurchaseInvoice::count())
                ->description('All purchase invoices in the system')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
                
            Stat::make('This Month\'s Invoices', PurchaseInvoice::whereMonth('created_at', now()->month)->count())
                ->description('Purchase invoices created this month')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
                
            Stat::make('Total Purchase Value', '$' . number_format(PurchaseInvoice::sum('total_amount'), 2))
                ->description('Total value of all purchases')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
                
            Stat::make('Average Invoice Value', '$' . number_format(PurchaseInvoice::avg('total_amount') ?? 0, 2))
                ->description('Average purchase invoice amount')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}
