<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\FontWeight;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;
    
    protected static ?string $title = 'View User';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->color('warning'),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('User Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Full Name')
                            ->weight(FontWeight::Bold)
                            ->size('lg'),
                            
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->copyable()
                            ->icon('heroicon-o-envelope'),
                            
                        TextEntry::make('email_verified_at')
                            ->label('Email Verification')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Not verified yet')
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'danger'),
                            
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d/m/Y H:i'),
                            
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
                    
                Section::make('Associated Roles')
                    ->schema([
                        RepeatableEntry::make('roles')
                            ->label('Roles List')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Role Name')
                                    ->badge()
                                    ->color('info'),
                                    
                                TextEntry::make('guard_name')
                                    ->label('Guard Name')
                                    ->badge()
                                    ->color('success'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
                Section::make('Direct Permissions')
                    ->schema([
                        RepeatableEntry::make('permissions')
                            ->label('Permissions List')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Permission Name')
                                    ->badge()
                                    ->color('warning'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
                Section::make('Statistics')
                    ->schema([
                        TextEntry::make('roles_count')
                            ->label('Roles Count')
                            ->state(fn ($record) => $record->roles()->count())
                            ->badge()
                            ->color('info'),
                            
                        TextEntry::make('permissions_count')
                            ->label('Direct Permissions Count')
                            ->state(fn ($record) => $record->permissions()->count())
                            ->badge()
                            ->color('warning'),
                            
                        TextEntry::make('all_permissions_count')
                            ->label('Total Permissions')
                            ->state(fn ($record) => $record->getAllPermissions()->count())
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(3),
            ]);
    }
}