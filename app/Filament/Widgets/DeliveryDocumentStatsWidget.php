<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\DeliveryDocument;

class DeliveryDocumentStatsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalDeliveries = DeliveryDocument::count();
        $todayDeliveries = DeliveryDocument::whereDate('date_and_time', today())->count();
        $thisMonthDeliveries = DeliveryDocument::whereMonth('date_and_time', now()->month)
            ->whereYear('date_and_time', now()->year)->count();
        $totalRevenue = DeliveryDocument::with('deliveryDocumentProducts')
            ->get()
            ->sum(function ($delivery) {
                return $delivery->deliveryDocumentProducts->sum(function ($product) {
                    return $product->quantity * $product->unit_price * (1 + ($product->tax_rate / 100));
                });
            });
        
        return [
            Stat::make('Total Deliveries', $totalDeliveries)
                ->description('All delivery documents')
                ->descriptionIcon('heroicon-m-document-arrow-up')
                ->color('primary'),
            Stat::make('Today Deliveries', $todayDeliveries)
                ->description('Deliveries today')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
            Stat::make('This Month', $thisMonthDeliveries)
                ->description('Deliveries this month')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('Total delivery revenue')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info'),
        ];
    }
}
