<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\SalesInvoice;
use Carbon\Carbon;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Monthly Sales Trends';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'md';

    protected function getData(): array
    {
        $now = Carbon::now();
        $months = [];
        $salesData = [];

        // Get last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $months[] = $month->format('M Y');
            
            $sales = SalesInvoice::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
                
            $salesData[] = $sales ?: 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales Amount',
                    'data' => $salesData,
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.2)',
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
