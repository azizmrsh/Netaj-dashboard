<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;
    
    protected static ?string $title = 'Create New Role';
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Role Created Successfully')
            ->body('The new role has been created and assigned the specified permissions.');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure guard_name exists
        $data['guard_name'] = $data['guard_name'] ?? 'web';
        
        return $data;
    }
    
    protected function afterCreate(): void
    {
        // Additional logic can be added after role creation
        // such as logging the operation
    }
}