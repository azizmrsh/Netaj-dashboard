<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Roles', Role::count())
                ->description('All roles in the system')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary'),
                
            Stat::make('Total Permissions', Permission::count())
                ->description('All permissions available')
                ->descriptionIcon('heroicon-m-key')
                ->color('success'),
                
            Stat::make('Admin Roles', Role::where('name', 'like', '%admin%')->count())
                ->description('Roles with admin privileges')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('warning'),
                
            Stat::make('User Roles', Role::where('name', 'like', '%user%')->count())
                ->description('Regular user roles')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
