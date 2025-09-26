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
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Average;
use Carbon\Carbon;
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
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $receiptDocument = ReceiptDocument::with(['supplier', 'receiptDocumentProducts.product'])->find($state);
                                    if ($receiptDocument && $receiptDocument->supplier) {
                                        $supplier = $receiptDocument->supplier;
                                        $set('supplier_name', $supplier->name);
                                        $set('supplier_address', $supplier->address);
                                        $set('supplier_phone', $supplier->phone);
                                        $set('supplier_tax_number', $supplier->tax_number);
                                        
                                        // Load products from receipt document
                                        $products = [];
                                        foreach ($receiptDocument->receiptDocumentProducts as $receiptProduct) {
                                            $products[] = [
                                                'product_name' => $receiptProduct->product->name,
                                                'quantity' => $receiptProduct->quantity,
                                                'unit_price' => $receiptProduct->unit_price ?? 0,
                                                'subtotal' => ($receiptProduct->quantity * ($receiptProduct->unit_price ?? 0)),
                                            ];
                                        }
                                        $set('products', $products);
                                    }
                                } else {
                                    $set('products', []);
                                }
                            }),
                    ])->columns(3),
                    
                Section::make('Supplier & Payment Information')
                    ->description('Supplier details and payment information')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('supplier_name')
                                    ->label('Supplier Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Supplier full name'),
                                TextInput::make('supplier_address')
                                    ->label('Supplier Address')
                                    ->maxLength(255)
                                    ->placeholder('Supplier address'),
                                TextInput::make('supplier_phone')
                                    ->label('Supplier Phone')
                                    ->maxLength(255)
                                    ->placeholder('Supplier phone number'),
                                TextInput::make('supplier_tax_number')
                                    ->label('Supplier Tax Number')
                                    ->maxLength(255)
                                    ->placeholder('Supplier tax registration number'),
                            ]),
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
                    
                Section::make('Products')
                    ->description('Products from selected receipt document')
                    ->schema([
                        Repeater::make('products')
                            ->label('Products')
                            ->schema([
                                TextInput::make('product_name')
                                    ->label('Product Name')
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false),
                                TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->prefix('SAR')
                                    ->step(0.01)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $quantity = $get('quantity') ?? 0;
                                        $unitPrice = $state ?? 0;
                                        $subtotal = $quantity * $unitPrice;
                                        $set('subtotal', $subtotal);
                                        
                                        // Calculate totals for all products
                                        $allProducts = $get('../../products') ?? [];
                                        $totalSubtotal = 0;
                                        
                                        foreach ($allProducts as $product) {
                                            $totalSubtotal += ($product['subtotal'] ?? 0);
                                        }
                                        
                                        $set('../../subtotal_amount', $totalSubtotal);
                                        
                                        // Calculate tax amount
                                        $taxRate = $get('../../tax_rate') ?? 15;
                                        $taxAmount = ($totalSubtotal * $taxRate) / 100;
                                        $set('../../total_tax_amount', $taxAmount);
                                        
                                        // Calculate total amount
                                        $discountAmount = $get('../../discount_amount') ?? 0;
                                        $totalAmount = $totalSubtotal + $taxAmount - $discountAmount;
                                        $set('../../total_amount_with_tax', $totalAmount);
                                    }),
                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('SAR')
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->columns(4)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),
                    
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
                        TextInput::make('tax_rate')
                            ->label('Tax Rate (%)')
                            ->numeric()
                            ->suffix('%')
                            ->default(15.00)
                            ->step(0.01)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $subtotal = $get('subtotal_amount') ?? 0;
                                $taxRate = $state ?? 15;
                                $taxAmount = ($subtotal * $taxRate) / 100;
                                $set('total_tax_amount', $taxAmount);
                                
                                $discountAmount = $get('discount_amount') ?? 0;
                                $totalAmount = $subtotal + $taxAmount - $discountAmount;
                                $set('total_amount_with_tax', $totalAmount);
                            }),
                        TextInput::make('total_tax_amount')
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
                            ->default(0.00)
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $subtotal = $get('subtotal_amount') ?? 0;
                                $taxAmount = $get('total_tax_amount') ?? 0;
                                $discountAmount = $state ?? 0;
                                $totalAmount = $subtotal + $taxAmount - $discountAmount;
                                $set('total_amount_with_tax', $totalAmount);
                            }),
                        TextInput::make('total_amount_with_tax')
                            ->label('Total Amount with Tax')
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
                    ->copyMessage('Invoice number copied')
                    ->copyMessageDuration(1500)
                    ->weight(FontWeight::Bold),
                TextColumn::make('receiptDocument.supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                TextColumn::make('date_and_time')
                    ->label('Invoice Date')
                    ->dateTime('M j, Y g:i A')
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
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'paid' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-m-pencil-square',
                        'sent' => 'heroicon-m-paper-airplane',
                        'paid' => 'heroicon-m-check-circle',
                        'cancelled' => 'heroicon-m-x-circle',
                    }),
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),
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
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(2)
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
                    FilamentExportBulkAction::make('export')
                        ->label('Export Selected')
                        ->color('success')
                        ->icon('heroicon-o-arrow-down-tray'),
                ]),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('Export All')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray'),
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

    public static function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\PurchaseInvoiceStatsWidget::class,
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
