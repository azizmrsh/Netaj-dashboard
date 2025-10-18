<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\DeliveryDocument;
use App\Models\ReceiptDocument;

class SystemOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Active system users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            
            Stat::make('Total Products', Product::count())
                ->description('Products in inventory')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info')
                ->chart([15, 4, 10, 2, 12, 4, 12]),
            
            Stat::make('Active Customers', Customer::customers()->count())
                ->description('Registered customers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning')
                ->chart([3, 8, 5, 10, 6, 12, 8]),
            
            Stat::make('Active Suppliers', Customer::suppliers()->count())
                ->description('Registered suppliers')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('danger')
                ->chart([2, 6, 4, 8, 3, 9, 5]),
        ];
    }
}
