<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Card;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationLabel = 'Roles';
    
    protected static ?string $modelLabel = 'Role';
    
    protected static ?string $pluralModelLabel = 'Roles';
    
    protected static ?string $navigationGroup = 'User Management';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Role Information')
                    ->description('Define the role name and description')
                    ->schema([
                        TextInput::make('name')
                            ->label('Role Name')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Example: Admin, Editor, Viewer')
                            ->helperText('Unique name for the role'),
                        
                        TextInput::make('guard_name')
                            ->label('Guard Name')
                            ->default('web')
                            ->required()
                            ->disabled()
                            ->helperText('Type of protection used'),
                    ])
                    ->columns(2),
                    
                Section::make('Permissions')
                    ->description('Select the permissions available for this role')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label('Available Permissions')
                            ->relationship('permissions', 'name')
                            ->options(function () {
                                return Permission::all()->groupBy(function ($permission) {
                                    $parts = explode('_', $permission->name);
                                    return end($parts);
                                })->map(function ($group, $key) {
                                    return $group->pluck('name', 'name')->toArray();
                                })->flatten();
                            })
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable()
                            ->searchable()
                            ->helperText('Choose the permissions this role can access'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                TextColumn::make('guard_name')
                    ->label('Guard Name')
                    ->badge()
                    ->color('success'),
                    
                TextColumn::make('permissions_count')
                    ->label('Permissions Count')
                    ->counts('permissions')
                    ->badge()
                    ->color('info'),
                    
                TextColumn::make('users_count')
                    ->label('Users Count')
                    ->counts('users')
                    ->badge()
                    ->color('warning'),
                    
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->label('View'),
                EditAction::make()
                    ->label('Edit'),
                DeleteAction::make()
                    ->label('Delete')
                    ->requiresConfirmation()
                    ->modalHeading('Delete Role')
                    ->modalDescription('Are you sure you want to delete this role? This action cannot be undone.')
                    ->modalSubmitActionLabel('Delete')
                    ->modalCancelActionLabel('Cancel'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Roles')
                        ->modalDescription('Are you sure you want to delete the selected roles? This action cannot be undone.')
                        ->modalSubmitActionLabel('Delete')
                        ->modalCancelActionLabel('Cancel'),
                ]),
            ])
            ->emptyStateHeading('No Roles Found')
            ->emptyStateDescription('Start by creating a new role to manage user permissions')
            ->emptyStateIcon('heroicon-o-shield-check');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_role');
    }
    
    public static function canCreate(): bool
    {
        return auth()->user()->can('create_role');
    }
    
    public static function canEdit($record): bool
    {
        return auth()->user()->can('update_role');
    }
    
    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_role');
    }
    
    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_any_role');
    }
}