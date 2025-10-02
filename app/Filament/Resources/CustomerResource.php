<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Customers & Suppliers Management';

    protected static ?string $modelLabel = 'Customer';

    protected static ?string $pluralModelLabel = 'Customers';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name_company')
                            ->label('Company Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
                
                Forms\Components\Section::make('Supplier Relationship')
                    ->schema([
                        Forms\Components\Toggle::make('is_supplier')
                            ->label('Is also a Supplier')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (!$state) {
                                    $set('supplier_id', null);
                                }
                            }),
                        
                        Forms\Components\Select::make('supplier_id')
                            ->label('Linked Supplier')
                            ->options(Supplier::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn (Forms\Get $get): bool => $get('is_supplier'))
                            ->helperText('Select the supplier record this customer is linked to'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Address & Location')
                    ->schema([
                        Forms\Components\TextInput::make('country')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zip_code')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Legal Information')
                    ->schema([
                        Forms\Components\TextInput::make('tax_number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('national_number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('commercial_registration_number')
                            ->label('Commercial Registration')
                            ->maxLength(255),
                    ])->columns(2),
                
                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name_company')
                    ->label('Company')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\IconColumn::make('is_supplier')
                    ->label('Is Supplier')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Linked Supplier')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Not linked'),
                Tables\Columns\TextColumn::make('country')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tax_number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                Tables\Filters\Filter::make('has_company')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('name_company'))
                    ->label('Has Company Name'),
                Tables\Filters\TernaryFilter::make('is_supplier')
                    ->label('Is Supplier')
                    ->boolean()
                    ->trueLabel('Is Supplier')
                    ->falseLabel('Not Supplier')
                    ->native(false),
                Tables\Filters\Filter::make('has_supplier_link')
                    ->label('Has Supplier Link')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('supplier_id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected'),
                ]),
            ])
            ->emptyStateHeading('No customers yet')
            ->emptyStateDescription('Get started by creating a new customer.')
            ->emptyStateIcon('heroicon-o-users');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\CustomerStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}