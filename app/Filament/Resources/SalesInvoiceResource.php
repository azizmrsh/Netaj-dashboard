<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesInvoiceResource\Pages;
use App\Filament\Resources\SalesInvoiceResource\RelationManagers;
use App\Models\SalesInvoice;
use App\Models\DeliveryDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesInvoiceResource extends Resource
{
    protected static ?string $model = SalesInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Sales Invoices';

    protected static ?string $modelLabel = 'Sales Invoice';

    protected static ?string $pluralModelLabel = 'Sales Invoices';

    protected static ?string $navigationGroup = 'Invoice Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Invoice Information')
                    ->description('Basic invoice information')
                    ->schema([
                        TextInput::make('invoice_no')
                            ->label('Invoice Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('Example: INV-2024-001'),
                        DateTimePicker::make('invoice_date')
                            ->label('Invoice Date & Time')
                            ->required()
                            ->default(now()),
                        Select::make('delivery_document_id')
                            ->label('Delivery Document')
                            ->relationship('deliveryDocument', 'id')
                            ->getOptionLabelFromRecordUsing(fn (DeliveryDocument $record): string => "Delivery #{$record->id} - {$record->customer->name}")
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(3),
                    
                Section::make('Customer & Payment Information')
                    ->description('Customer details and payment information')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Customer full name'),
                        TextInput::make('customer_address')
                            ->label('Customer Address')
                            ->maxLength(255)
                            ->placeholder('Customer address'),
                        TextInput::make('customer_phone')
                            ->label('Customer Phone')
                            ->maxLength(255)
                            ->placeholder('Customer phone number'),
                        TextInput::make('customer_tax_number')
                            ->label('Customer Tax Number')
                            ->maxLength(255)
                            ->placeholder('Customer tax registration number'),
                        TextInput::make('payment_method')
                            ->label('Payment Method')
                            ->maxLength(255)
                            ->placeholder('Example: Cash, Credit Card, Bank Transfer'),
                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->placeholder('Invoice due date'),
                    ])->columns(2),
                    
                Section::make('Financial Totals')
                    ->description('Amount and tax totals')
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal Amount')
                            ->numeric()
                            ->prefix('SAR')
                            ->default(0.00)
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('tax_rate')
                            ->label('Tax Rate (%)')
                            ->numeric()
                            ->suffix('%')
                            ->default(15.00)
                            ->step(0.01),
                        TextInput::make('tax_amount')
                            ->label('Total Tax Amount')
                            ->numeric()
                            ->prefix('SAR')
                            ->default(0.00)
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('discount_amount')
                            ->label('Discount Amount')
                            ->numeric()
                            ->prefix('SAR')
                            ->default(0.00),
                        TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->prefix('SAR')
                            ->default(0.00)
                            ->disabled()
                            ->dehydrated(),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'paid' => 'Paid',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(2),
                    
                Section::make('Additional Notes')
                    ->description('Additional notes and details')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Any additional notes about the invoice...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_no')
                    ->label('Invoice Number')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable(),
                
                TextColumn::make('invoice_date')
                    ->label('Invoice Date')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('deliveryDocument.id')
                    ->label('Delivery Document')
                    ->formatStateUsing(fn ($state) => "Delivery #{$state}")
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('customer_name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                
                TextColumn::make('customer_phone')
                    ->label('Customer Phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('SAR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('tax_rate')
                    ->label('Tax Rate')
                    ->formatStateUsing(fn ($state) => $state ? "{$state}%" : 'N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('tax_amount')
                    ->label('Tax Amount')
                    ->money('SAR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('discount_amount')
                    ->label('Discount')
                    ->money('SAR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('SAR')
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->color('success'),
                
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M j, Y')
                    ->sortable()
                    ->color(fn ($state) => $state && $state < now() ? 'danger' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Created From'),
                        DatePicker::make('created_until')
                            ->label('Created Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Filter::make('total_amount')
                    ->form([
                        TextInput::make('amount_from')
                            ->label('Amount From')
                            ->numeric()
                            ->prefix('SAR'),
                        TextInput::make('amount_to')
                            ->label('Amount To')
                            ->numeric()
                            ->prefix('SAR'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount', '<=', $amount),
                            );
                    }),
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['deliveryDocument']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['invoice_no', 'customer_name', 'customer_phone', 'notes'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Customer' => $record->customer_name,
            'Total Amount' => 'SAR ' . number_format($record->total_amount, 2),
            'Status' => ucfirst($record->status),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DeliveryDocumentProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalesInvoices::route('/'),
            'create' => Pages\CreateSalesInvoice::route('/create'),
            'view' => Pages\ViewSalesInvoice::route('/{record}'),
            'edit' => Pages\EditSalesInvoice::route('/{record}/edit'),
        ];
    }
}
