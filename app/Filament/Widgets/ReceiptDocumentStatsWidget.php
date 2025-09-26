<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ReceiptDocument;

class ReceiptDocumentStatsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalReceipts = ReceiptDocument::count();
        $todayReceipts = ReceiptDocument::whereDate('date_and_time', today())->count();
        $thisMonthReceipts = ReceiptDocument::whereMonth('date_and_time', now()->month)
            ->whereYear('date_and_time', now()->year)->count();
        $totalRevenue = ReceiptDocument::with('receiptDocumentProducts')
            ->get()
            ->sum(function ($receipt) {
                return $receipt->receiptDocumentProducts->sum(function ($product) {
                    return $product->quantity * $product->unit_price * (1 + ($product->tax_rate / 100));
                });
            });
        
        return [
            Stat::make('Total Receipts', $totalReceipts)
                ->description('All receipt documents')
                ->descriptionIcon('heroicon-m-document-arrow-down')
                ->color('primary'),
            Stat::make('Today Receipts', $todayReceipts)
                ->description('Receipts today')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
            Stat::make('This Month', $thisMonthReceipts)
                ->description('Receipts this month')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('Total receipt revenue')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
        ];
    }
}
