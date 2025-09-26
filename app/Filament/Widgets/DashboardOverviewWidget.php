<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\DeliveryDocument;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\ReceiptDocument;
use App\Models\SalesInvoice;
use App\Models\Supplier;
use App\Models\Transporter;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DashboardOverviewWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    
    protected function getStats(): array
    {
        // حساب الإحصائيات الأساسية
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('is_active', true)->count();
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::where('is_active', true)->count();
        $totalTransporters = Transporter::count();
        $activeTransporters = Transporter::where('is_active', true)->count();
        $totalUsers = User::count();
        $totalRoles = Role::count();
        $activeRoles = Role::whereHas('users')->count();
        
        // حساب إحصائيات المستندات
        $totalReceiptDocuments = ReceiptDocument::count();
        $todayReceiptDocuments = ReceiptDocument::whereDate('created_at', today())->count();
        $totalDeliveryDocuments = DeliveryDocument::count();
        $todayDeliveryDocuments = DeliveryDocument::whereDate('created_at', today())->count();
        
        // حساب إحصائيات الفواتير
        $totalSalesInvoices = SalesInvoice::count();
        $todaySalesInvoices = SalesInvoice::whereDate('created_at', today())->count();
        $totalPurchaseInvoices = PurchaseInvoice::count();
        $todayPurchaseInvoices = PurchaseInvoice::whereDate('created_at', today())->count();
        
        // حساب المبالغ المالية
        $totalSalesAmount = SalesInvoice::sum('total_amount') ?? 0;
        $todaySalesAmount = SalesInvoice::whereDate('created_at', today())->sum('total_amount') ?? 0;
        $totalPurchaseAmount = PurchaseInvoice::sum('total_amount_with_tax') ?? 0;
        $todayPurchaseAmount = PurchaseInvoice::whereDate('created_at', today())->sum('total_amount_with_tax') ?? 0;
        
        return [
            // 1. Total Products
            Stat::make('Total Products', $totalProducts)
                ->description($activeProducts . ' active products')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            
            // 2. Total Customers
            Stat::make('Total Customers', $totalCustomers)
                ->description($activeCustomers . ' active customers')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart([15, 4, 10, 2, 12, 4, 12]),
            
            // 3. Total Suppliers
            Stat::make('Total Suppliers', $totalSuppliers)
                ->description($activeSuppliers . ' active suppliers')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
            
            // 4. Transport Companies
            Stat::make('Transport Companies', $totalTransporters)
                ->description($activeTransporters . ' active companies')
                ->descriptionIcon('heroicon-m-map')
                ->color('primary')
                ->chart([2, 1, 3, 2, 4, 2, 3]),
            
            // 5. Receipt Documents
            Stat::make('Receipt Documents', $totalReceiptDocuments)
                ->description($todayReceiptDocuments . ' today')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success')
                ->chart([3, 5, 2, 8, 4, 6, 7]),
            
            // 6. Delivery Documents
            Stat::make('Delivery Documents', $totalDeliveryDocuments)
                ->description($todayDeliveryDocuments . ' today')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info')
                ->chart([4, 3, 6, 5, 8, 4, 9]),
            
            // 7. Sales Invoices
            Stat::make('Sales Invoices', $totalSalesInvoices)
                ->description($todaySalesInvoices . ' invoices today')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([5, 8, 3, 12, 6, 10, 14]),
            
            // 8. Purchase Invoices
            Stat::make('Purchase Invoices', $totalPurchaseInvoices)
                ->description($todayPurchaseInvoices . ' invoices today')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('warning')
                ->chart([3, 6, 4, 8, 5, 7, 9]),
            
            // 9. Total Sales
            Stat::make('Total Sales', 'SAR ' . number_format($totalSalesAmount, 2))
                ->description('Today: SAR ' . number_format($todaySalesAmount, 2))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([1000, 1500, 800, 2200, 1200, 1800, 2500]),
            
            // 10. Total Purchases
            Stat::make('Total Purchases', 'SAR ' . number_format($totalPurchaseAmount, 2))
                ->description('Today: SAR ' . number_format($todayPurchaseAmount, 2))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([800, 1200, 600, 1800, 900, 1400, 2000]),
            
            // 11. System Roles
            Stat::make('System Roles', $totalRoles)
                ->description($activeRoles . ' roles with users')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('info')
                ->chart([1, 2, 2, 3, 3, 4, $totalRoles]),
            
            // 12. System Users
            Stat::make('System Users', $totalUsers)
                ->description('Total users in system')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([1, 1, 2, 2, 3, 3, $totalUsers]),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4; // عرض 4 widgets في كل صف
    }
}