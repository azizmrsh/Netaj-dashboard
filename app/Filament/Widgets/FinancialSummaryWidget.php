<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use Illuminate\Support\Facades\DB;

class FinancialSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 8;

    protected function getStats(): array
    {
        // Calculate total sales for current month
        $currentMonthSales = SalesInvoice::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
            
        // Calculate total purchases for current month
        $currentMonthPurchases = PurchaseInvoice::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount_with_tax');
            
        // Calculate profit/loss for current month
        $currentMonthProfit = $currentMonthSales - $currentMonthPurchases;
        
        // Calculate total revenue (all time)
        $totalRevenue = SalesInvoice::sum('total_amount');
        
        // Calculate profit margin percentage
        $profitMargin = $currentMonthSales > 0 ? (($currentMonthProfit / $currentMonthSales) * 100) : 0;
        
        // Get last 7 days sales for chart
        $salesChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailySales = SalesInvoice::whereDate('created_at', $date)->sum('total_amount');
            $salesChart[] = $dailySales / 1000; // Convert to thousands for better chart display
        }
        
        return [
            Stat::make('Monthly Sales', number_format($currentMonthSales, 0) . ' IQD')
                ->description('Sales for ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($salesChart),
                
            Stat::make('Monthly Purchases', number_format($currentMonthPurchases, 0) . ' IQD')
                ->description('Purchases for ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info')
                ->chart([15, 18, 12, 20, 16, 14, 22]),
                
            Stat::make('Monthly Profit/Loss', number_format($currentMonthProfit, 0) . ' IQD')
                ->description($currentMonthProfit >= 0 ? 'Profit this month' : 'Loss this month')
                ->descriptionIcon($currentMonthProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($currentMonthProfit >= 0 ? 'success' : 'danger')
                ->chart($currentMonthProfit >= 0 ? [5, 8, 12, 15, 18, 20, 25] : [25, 20, 18, 15, 12, 8, 5]),
                
            Stat::make('Profit Margin', number_format($profitMargin, 1) . '%')
                ->description('Current month margin')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color($profitMargin >= 20 ? 'success' : ($profitMargin >= 10 ? 'warning' : 'danger'))
                ->chart([10, 15, 18, 22, 25, 28, 30]),
        ];
    }
}
