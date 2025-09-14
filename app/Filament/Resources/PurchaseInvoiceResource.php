<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseInvoiceResource\Pages;
use App\Filament\Resources\PurchaseInvoiceResource\RelationManagers;
use App\Models\PurchaseInvoice;
use App\Models\ReceiptDocument;
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

class PurchaseInvoiceResource extends Resource
{
    protected static ?string $model = PurchaseInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Purchase Invoices';
    
    protected static ?string $modelLabel = 'Purchase Invoice';
    
    protected static ?string $pluralModelLabel = 'Purchase Invoices';
    
    protected static ?string $navigationGroup = 'Invoice Management';
    
    protected static ?int $navigationSort = 1;

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
                        DateTimePicker::make('date_and_time')
                            ->label('Invoice Date & Time')
                            ->required()
                            ->default(now()),
                        Select::make('id_receipt_documents')
                            ->label('Receipt Document')
                            ->relationship('receiptDocument', 'id')
                            ->getOptionLabelFromRecordUsing(fn (ReceiptDocument $record): string => "Receipt #{$record->id} - {$record->supplier->name}")
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
                    
                Section::make('Payment & Supply Information')
                    ->description('Payment details and supply information')
                    ->schema([
                        TextInput::make('payment_terms')
                            ->label('Payment Terms')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Example: Cash, 30 days credit'),
                        TextInput::make('place_of_supply')
                            ->label('Place of Supply')
                            ->maxLength(255)
                            ->placeholder('Example: Riyadh Warehouse'),
                        TextInput::make('buyers_order_no')
                            ->label('Purchase Order Number')
                            ->maxLength(255)
                            ->placeholder('Example: PO-2024-001'),
                    ])->columns(2),
                    
                Section::make('Financial Totals')
                    ->description('Amount and tax totals')
                    ->schema([
                        TextInput::make('subtotal_amount')
                            ->label('Subtotal Amount')
                            ->numeric()
                            ->prefix('SAR')
                            ->default(0.00)
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('total_tax_amount')
                            ->label('Total Tax Amount')
                            ->numeric()
                            ->prefix('SAR')
                            ->default(0.00)
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('total_amount_with_tax')
                            ->label('Total Amount with Tax')
                            ->numeric()
                            ->prefix('SAR')
                            ->default(0.00)
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),
                    
                Section::make('Additional Notes')
                    ->description('Additional notes and details')
                    ->schema([
                        Textarea::make('note')
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
                    ->copyable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('receiptDocument.supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('date_and_time')
                    ->label('Invoice Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('payment_terms')
                    ->label('Payment Terms')
                    ->searchable()
                    ->toggleable()
                    ->limit(20),
                TextColumn::make('subtotal_amount')
                    ->label('Subtotal Amount')
                    ->money('SAR')
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('total_tax_amount')
                    ->label('Tax')
                    ->money('SAR')
                    ->sortable()
                    ->alignEnd()
                    ->toggleable(),
                TextColumn::make('total_amount_with_tax')
                    ->label('Total')
                    ->money('SAR')
                    ->sortable()
                    ->alignEnd()
                    ->weight(FontWeight::Bold)
                    ->color('success'),
                TextColumn::make('products_count')
                    ->label('Products Count')
                    ->counts('products')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('id_receipt_documents')
                    ->label('Receipt Document')
                    ->relationship('receiptDocument', 'id')
                    ->getOptionLabelFromRecordUsing(fn (ReceiptDocument $record): string => "Receipt #{$record->id}")
                    ->searchable()
                    ->preload(),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('From Date'),
                        DatePicker::make('created_until')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_and_time', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_and_time', '<=', $date),
                            );
                    }),
                Filter::make('amount_range')
                    ->form([
                        TextInput::make('amount_from')
                            ->label('From Amount')
                            ->numeric(),
                        TextInput::make('amount_to')
                            ->label('To Amount')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount_with_tax', '>=', $amount),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $amount): Builder => $query->where('total_amount_with_tax', '<=', $amount),
                            );
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->label('View'),
                EditAction::make()
                    ->label('Edit'),
                DeleteAction::make()
                    ->label('Delete'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Delete Selected'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReceiptDocumentProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseInvoices::route('/'),
            'create' => Pages\CreatePurchaseInvoice::route('/create'),
            'edit' => Pages\EditPurchaseInvoice::route('/{record}/edit'),
        ];
    }
    
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['receiptDocument.supplier']);
    }
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['invoice_no', 'receiptDocument.supplier.name', 'payment_terms'];
    }
    
    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Supplier' => $record->receiptDocument?->supplier?->name,
            'Date' => $record->date_and_time?->format('d/m/Y'),
            'Amount' => number_format($record->total_amount_with_tax, 2) . ' SAR',
        ];
    }
}
