<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;

class SystemHealthWidget extends BaseWidget
{
    protected static ?int $sort = 10;

    protected function getStats(): array
    {
        // Database connection check
        $dbStatus = $this->checkDatabaseConnection();
        
        // Calculate database size (approximate)
        $dbSize = $this->getDatabaseSize();
        
        // Get total records count
        $totalRecords = $this->getTotalRecords();
        
        // Cache status check
        $cacheStatus = $this->checkCacheStatus();
        
        // System uptime (approximate based on oldest record)
        $systemUptime = $this->getSystemUptime();
        
        return [
            Stat::make('Database Status', $dbStatus ? 'Connected' : 'Disconnected')
                ->description($dbStatus ? 'Database is healthy' : 'Database connection failed')
                ->descriptionIcon($dbStatus ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                ->color($dbStatus ? 'success' : 'danger')
                ->chart($dbStatus ? [1, 1, 1, 1, 1, 1, 1] : [0, 0, 0, 0, 0, 0, 0]),
                
            Stat::make('Total Records', number_format($totalRecords))
                ->description('Records across all tables')
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color('info')
                ->chart([100, 150, 200, 250, 300, 350, $totalRecords / 10]),
                
            Stat::make('Database Size', $dbSize)
                ->description('Approximate storage usage')
                ->descriptionIcon('heroicon-m-server')
                ->color($this->getDatabaseSizeColor($dbSize))
                ->chart([10, 15, 20, 25, 30, 35, 40]),
                
            Stat::make('Cache Status', $cacheStatus ? 'Active' : 'Inactive')
                ->description($cacheStatus ? 'Cache is working' : 'Cache not available')
                ->descriptionIcon($cacheStatus ? 'heroicon-m-bolt' : 'heroicon-m-exclamation-triangle')
                ->color($cacheStatus ? 'success' : 'warning')
                ->chart($cacheStatus ? [5, 8, 12, 15, 18, 20, 25] : [0, 0, 0, 0, 0, 0, 0]),
        ];
    }
    
    private function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function getDatabaseSize(): string
    {
        try {
            // Get database name
            $database = config('database.connections.mysql.database');
            
            // Query to get database size
            $result = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$database]);
            
            $sizeMB = $result[0]->size_mb ?? 0;
            
            if ($sizeMB > 1024) {
                return round($sizeMB / 1024, 2) . ' GB';
            } else {
                return $sizeMB . ' MB';
            }
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
    
    private function getDatabaseSizeColor(string $size): string
    {
        $numericSize = (float) str_replace([' MB', ' GB'], '', $size);
        
        if (str_contains($size, 'GB')) {
            return $numericSize > 5 ? 'danger' : ($numericSize > 2 ? 'warning' : 'success');
        } else {
            return $numericSize > 500 ? 'warning' : 'success';
        }
    }
    
    private function getTotalRecords(): int
    {
        try {
            $userCount = User::count();
            $productCount = Product::count();
            $salesCount = SalesInvoice::count();
            $purchaseCount = PurchaseInvoice::count();
            
            return $userCount + $productCount + $salesCount + $purchaseCount;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function checkCacheStatus(): bool
    {
        try {
            Cache::put('health_check', 'test', 1);
            $result = Cache::get('health_check');
            Cache::forget('health_check');
            
            return $result === 'test';
        } catch (\Exception $e) {
            return false;
        }
    }
    
    private function getSystemUptime(): string
    {
        try {
            $oldestRecord = User::orderBy('created_at')->first();
            
            if ($oldestRecord) {
                $uptime = now()->diffInDays($oldestRecord->created_at);
                return $uptime . ' days';
            }
            
            return 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
}
