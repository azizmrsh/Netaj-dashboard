<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
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
                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options(Customer::getTypes())
                            ->default(Customer::TYPE_CUSTOMER)
                            ->required()
                            ->helperText('Select whether this is a customer, supplier, or both'),
                        Forms\Components\TextInput::make('name')
                            ->label('Customer Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
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
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type_display')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Customer' => 'success',
                        'Supplier' => 'warning',
                        'Customer & Supplier' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->copyable(),
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
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
                
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        Customer::TYPE_CUSTOMER => 'Customer',
                        Customer::TYPE_SUPPLIER => 'Supplier',
                        Customer::TYPE_BOTH => 'Both',
                    ])
                    ->native(false),
                
                Tables\Filters\Filter::make('customers_only')
                    ->label('Customers Only')
                    ->query(fn (Builder $query): Builder => $query->customers()),
                
                Tables\Filters\Filter::make('suppliers_only')
                    ->label('Suppliers Only')
                    ->query(fn (Builder $query): Builder => $query->suppliers()),
                
                Tables\Filters\Filter::make('both_only')
                    ->label('Customer & Supplier')
                    ->query(fn (Builder $query): Builder => $query->both()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\Action::make('report')
                    ->label('Report')
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('info')
                    ->url(fn (Customer $record): string => route('filament.admin.resources.customers.report', ['customer' => $record->id]))
                    ->openUrlInNewTab(),
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
            'report' => Pages\CustomerReport::route('/{customer}/report'),
        ];
    }
}