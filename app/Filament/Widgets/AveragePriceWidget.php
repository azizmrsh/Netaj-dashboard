<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;

class AveragePriceWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $avgPrice1 = Product::whereNotNull('price1')->avg('price1');
        $avgPrice2 = Product::whereNotNull('price2')->avg('price2');
        
        return [
            Stat::make('Average Price 1', '$' . number_format($avgPrice1 ?? 0, 2))
                ->description('Average of Price 1 across all products')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
            Stat::make('Average Price 2', '$' . number_format($avgPrice2 ?? 0, 2))
                ->description('Average of Price 2 across all products')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
