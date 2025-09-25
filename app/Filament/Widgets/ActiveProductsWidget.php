<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;

class ActiveProductsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Active Products', Product::where('is_active', 1)->count())
                ->description('Currently active products')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
