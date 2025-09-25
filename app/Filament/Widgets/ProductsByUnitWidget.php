<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;

class ProductsByUnitWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $tonProducts = Product::where('unit', 'ton')->count();
        $barrelProducts = Product::where('unit', 'barrel')->count();
        
        return [
            Stat::make('Products by Ton', $tonProducts)
                ->description('Products measured in tons')
                ->descriptionIcon('heroicon-m-scale')
                ->color('warning'),
            Stat::make('Products by Barrel', $barrelProducts)
                ->description('Products measured in barrels')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('info'),
        ];
    }
}
