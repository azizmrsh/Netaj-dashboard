<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\FontWeight;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;
    
    protected static ?string $title = 'View Role';

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
                Section::make('Role Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Role Name')
                            ->weight(FontWeight::Bold)
                            ->size('lg'),
                            
                        TextEntry::make('guard_name')
                            ->label('Guard Name')
                            ->badge()
                            ->color('success'),
                            
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d/m/Y H:i'),
                            
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
                    
                Section::make('Associated Permissions')
                    ->schema([
                        RepeatableEntry::make('permissions')
                            ->label('Permissions List')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Permission Name')
                                    ->badge()
                                    ->color('info'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
                Section::make('Associated Users')
                    ->schema([
                        RepeatableEntry::make('users')
                            ->label('Users List')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('User Name')
                                    ->weight(FontWeight::Medium),
                                    
                                TextEntry::make('email')
                                    ->label('Email Address')
                                    ->color('gray'),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
                Section::make('Statistics')
                    ->schema([
                        TextEntry::make('permissions_count')
                            ->label('Permissions Count')
                            ->state(fn ($record) => $record->permissions()->count())
                            ->badge()
                            ->color('info'),
                            
                        TextEntry::make('users_count')
                            ->label('Users Count')
                            ->state(fn ($record) => $record->users()->count())
                            ->badge()
                            ->color('warning'),
                    ])
                    ->columns(2),
            ]);
    }
}