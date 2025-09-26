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
        
        // حساب الأرباح التقديرية
        $estimatedProfit = $totalSalesAmount - $totalPurchaseAmount;
        
        return [
            // 1. إجمالي المنتجات
            Stat::make('إجمالي المنتجات', $totalProducts)
                ->description($activeProducts . ' منتج نشط')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            
            // 2. إجمالي العملاء
            Stat::make('إجمالي العملاء', $totalCustomers)
                ->description($activeCustomers . ' عميل نشط')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart([15, 4, 10, 2, 12, 4, 12]),
            
            // 3. إجمالي الموردين
            Stat::make('إجمالي الموردين', $totalSuppliers)
                ->description($activeSuppliers . ' مورد نشط')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
            
            // 4. شركات النقل
            Stat::make('شركات النقل', $totalTransporters)
                ->description($activeTransporters . ' شركة نشطة')
                ->descriptionIcon('heroicon-m-map')
                ->color('primary')
                ->chart([2, 1, 3, 2, 4, 2, 3]),
            
            // 5. سندات الاستلام
            Stat::make('سندات الاستلام', $totalReceiptDocuments)
                ->description($todayReceiptDocuments . ' اليوم')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success')
                ->chart([3, 5, 2, 8, 4, 6, 7]),
            
            // 6. سندات التسليم
            Stat::make('سندات التسليم', $totalDeliveryDocuments)
                ->description($todayDeliveryDocuments . ' اليوم')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info')
                ->chart([4, 3, 6, 5, 8, 4, 9]),
            
            // 7. فواتير المبيعات
            Stat::make('فواتير المبيعات', $totalSalesInvoices)
                ->description($todaySalesInvoices . ' فاتورة اليوم')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([5, 8, 3, 12, 6, 10, 14]),
            
            // 8. فواتير المشتريات
            Stat::make('فواتير المشتريات', $totalPurchaseInvoices)
                ->description($todayPurchaseInvoices . ' فاتورة اليوم')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('warning')
                ->chart([3, 6, 4, 8, 5, 7, 9]),
            
            // 9. إجمالي مبيعات
            Stat::make('إجمالي المبيعات', number_format($totalSalesAmount, 2) . ' ر.س')
                ->description('اليوم: ' . number_format($todaySalesAmount, 2) . ' ر.س')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([1000, 1500, 800, 2200, 1200, 1800, 2500]),
            
            // 10. إجمالي مشتريات
            Stat::make('إجمالي المشتريات', number_format($totalPurchaseAmount, 2) . ' ر.س')
                ->description('اليوم: ' . number_format($todayPurchaseAmount, 2) . ' ر.س')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([800, 1200, 600, 1800, 900, 1400, 2000]),
            
            // 11. الأرباح التقديرية
            Stat::make('الأرباح التقديرية', number_format($estimatedProfit, 2) . ' ر.س')
                ->description($estimatedProfit >= 0 ? 'ربح' : 'خسارة')
                ->descriptionIcon($estimatedProfit >= 0 ? 'heroicon-m-arrow-up' : 'heroicon-m-arrow-down')
                ->color($estimatedProfit >= 0 ? 'success' : 'danger')
                ->chart([200, 300, 200, 400, 300, 400, 500]),
            
            // 12. المستخدمين
            Stat::make('المستخدمين', $totalUsers)
                ->description('إجمالي المستخدمين في النظام')
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