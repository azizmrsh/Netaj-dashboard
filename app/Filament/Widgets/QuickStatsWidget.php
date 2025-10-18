<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;

class QuickStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.quick-stats-widget';
    
    protected static ?int $sort = -1; // يظهر في الأعلى
    
    public function getStats(): array
    {
        return [
            'users_count' => User::count(),
            'products_count' => Product::count(),
            'customers_count' => Customer::customers()->count(),
            'suppliers_count' => Customer::suppliers()->count(),
        ];
    }
}