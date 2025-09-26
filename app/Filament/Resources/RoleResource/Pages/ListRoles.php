<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions\CreateAction;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;
    
    protected static ?string $title = 'Roles';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create New Role')
                ->icon('heroicon-o-plus')
                ->color('success'),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return RoleResource::getWidgets();
    }
}