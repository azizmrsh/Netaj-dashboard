<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Actions\DeleteAction;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;
    
    protected static ?string $title = 'Edit Role';

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('View')
                ->icon('heroicon-o-eye'),
            Actions\DeleteAction::make()
                ->label('Delete')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Delete Role')
                ->modalDescription('Are you sure you want to delete this role? All associated permissions will be removed from users.')
                ->modalSubmitActionLabel('Delete')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Role Updated Successfully')
            ->body('Changes to the role and its associated permissions have been saved.');
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure guard_name is not changed
        $data['guard_name'] = $this->record->guard_name;
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Additional logic can be added after saving changes
        // such as logging the operation or sending notifications
    }
}