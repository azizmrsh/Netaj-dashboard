<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\DeliveryDocumentProduct;
use App\Models\ReceiptDocumentProduct;

class InventoryStatusWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected function getStats(): array
    {
        // Calculate total products
        $totalProducts = Product::count();
        
        // Calculate products with recent sales (last 30 days)
        $activeSalesProducts = DeliveryDocumentProduct::whereHas('deliveryDocument', function($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })->distinct('product_id')->count();
        
        // Calculate products with recent purchases (last 30 days)
        $activePurchaseProducts = ReceiptDocumentProduct::whereHas('receiptDocument', function($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })->distinct('product_id')->count();
        
        // Calculate products without recent activity (potential slow movers)
        $slowMovingProducts = $totalProducts - max($activeSalesProducts, $activePurchaseProducts);
        
        return [
            Stat::make('Total Products', $totalProducts)
                ->description('All products in system')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
                
            Stat::make('Active Sales Products', $activeSalesProducts)
                ->description('Products sold in last 30 days')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([3, 5, 8, 12, 15, 18, 20]),
                
            Stat::make('Active Purchase Products', $activePurchaseProducts)
                ->description('Products purchased in last 30 days')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info')
                ->chart([2, 4, 6, 8, 10, 12, 14]),
                
            Stat::make('Slow Moving Products', $slowMovingProducts)
                ->description('Products with no recent activity')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($slowMovingProducts > 0 ? 'warning' : 'success')
                ->chart([10, 8, 6, 4, 3, 2, 1]),
        ];
    }
}
