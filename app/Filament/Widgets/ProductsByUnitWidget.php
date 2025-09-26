<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;

class ProductsByUnitWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', 1)->count();
        $tonProducts = Product::where('unit', 'ton')->count();
        $barrelProducts = Product::where('unit', 'barrel')->count();
        
        return [
            Stat::make('Total Products', $totalProducts)
                ->description('Total number of products')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),
            Stat::make('Active Products', $activeProducts)
                ->description('Currently active products')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
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
