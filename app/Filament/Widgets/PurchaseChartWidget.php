<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PurchaseInvoice;
use Carbon\Carbon;

class PurchaseChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Monthly Purchase Trends';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'md';

    protected function getData(): array
    {
        $now = Carbon::now();
        $months = [];
        $purchaseData = [];

        // Get last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $months[] = $month->format('M Y');
            
            $purchases = PurchaseInvoice::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount_with_tax');
                
            $purchaseData[] = $purchases ?: 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Purchase Amount',
                    'data' => $purchaseData,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.2)',
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
