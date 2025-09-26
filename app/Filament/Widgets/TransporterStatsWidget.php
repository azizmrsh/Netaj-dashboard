<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transporter;

class TransporterStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Transporters', Transporter::count())
                ->description('All transporters in the system')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
                
            Stat::make('Active Transporters', Transporter::where('is_active', 1)->count())
                ->description('Currently active transporters')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Transporters with Tax Numbers', Transporter::whereNotNull('tax_number')->count())
                ->description('Transporters with tax numbers')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
                
            Stat::make('Transporters with Phone', Transporter::whereNotNull('phone')->count())
                ->description('Transporters with phone numbers')
                ->descriptionIcon('heroicon-m-phone')
                ->color('warning'),
        ];
    }
}
