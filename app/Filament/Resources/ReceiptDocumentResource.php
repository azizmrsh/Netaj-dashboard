<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceiptDocumentResource\Pages;
use App\Filament\Resources\ReceiptDocumentResource\RelationManagers;
use App\Models\ReceiptDocument;
use App\Models\Supplier;
use App\Models\Transporter;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Components\Actions\Action;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReceiptDocumentResource extends Resource
{
    protected static ?string $model = ReceiptDocument::class;

     protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationGroup = 'Document Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DateTimePicker::make('date_and_time')
                                    ->required()
                                    ->label('Date and Time'),
                                Forms\Components\Select::make('id_supplier')
                                    ->relationship('supplier', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\Section::make('Basic Supplier Information')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Supplier Name')
                                                    ->required()
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('phone')
                                                    ->label('Phone Number')
                                                    ->tel()
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('email')
                                                    ->label('Email Address')
                                                    ->email()
                                                    ->maxLength(255),
                                                
                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Active')
                                                    ->default(true),
                                            ])
                                            ->columns(2),
                                        
                                        Forms\Components\Section::make('Company Information')
                                            ->schema([
                                                Forms\Components\TextInput::make('name_company')
                                                    ->label('Company Name')
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('tax_number')
                                                    ->label('Tax Number')
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('commercial_registration_number')
                                                    ->label('Commercial Registration Number')
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('national_number')
                                                    ->label('National Number')
                                                    ->maxLength(255),
                                            ])
                                            ->columns(2),
                                        
                                        Forms\Components\Section::make('Address Information')
                                            ->schema([
                                                Forms\Components\TextInput::make('country')
                                                    ->label('Country')
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('zip_code')
                                                    ->label('Zip Code')
                                                    ->maxLength(255),
                                                
                                                Forms\Components\Textarea::make('address')
                                                    ->label('Address')
                                                    ->rows(3)
                                                    ->columnSpanFull(),
                                            ])
                                            ->columns(2),
                                        
                                        Forms\Components\Section::make('Notes')
                                            ->schema([
                                                Forms\Components\Textarea::make('note')
                                                    ->label('Notes')
                                                    ->rows(4)
                                                    ->columnSpanFull(),
                                            ]),
                                    ])
                                    ->label('Supplier'),
                                Forms\Components\Select::make('id_transporter')
                                    ->relationship('transporter', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\Section::make('Basic Information')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Transporter Name')
                                                    ->required()
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('phone')
                                                    ->label('Phone Number')
                                                    ->required()
                                                    ->tel()
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('email')
                                                    ->label('Email Address')
                                                    ->email()
                                                    ->maxLength(255),
                                                
                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Active Status')
                                                    ->default(true),
                                            ])->columns(2),
                                        
                                        Forms\Components\Section::make('Additional Information')
                                            ->schema([
                                                Forms\Components\TextInput::make('id_number')
                                                    ->label('ID Number')
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('tax_number')
                                                    ->label('Tax Number')
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('driver_name')
                                                    ->label('Driver Name')
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('document_no')
                                                    ->label('Document Number')
                                                    ->maxLength(255),
                                                
                                                Forms\Components\TextInput::make('car_no')
                                                    ->label('Car Number')
                                                    ->maxLength(255),
                                            ])->columns(2),
                                        
                                        Forms\Components\Section::make('Notes')
                                            ->schema([
                                                Forms\Components\Textarea::make('note')
                                                    ->label('Notes')
                                                    ->rows(3)
                                                    ->columnSpanFull(),
                                            ]),
                                    ])
                                    ->label('Transporter'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('purchase_invoice_no')
                                    ->label('Purchase Invoice Number'),
                                Forms\Components\TextInput::make('material_source')
                                    ->label('Material Source'),
                            ]),
                        Forms\Components\Repeater::make('receiptDocumentProducts')
                            ->relationship()
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->relationship('product', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                Forms\Components\Section::make('Basic Product Information')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Product Name')
                                                            ->required()
                                                            ->maxLength(255),
                                                        
                                                        Forms\Components\TextInput::make('product_code')
                                                            ->label('Product Code')
                                                            ->required()
                                                            ->unique(ignoreRecord: true)
                                                            ->maxLength(255),
                                                        
                                                        Forms\Components\Textarea::make('description')
                                                            ->label('Description')
                                                            ->rows(3)
                                                            ->columnSpanFull(),
                                                    ])
                                                    ->columns(2),
                                                
                                                Forms\Components\Section::make('Product Specifications')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('performance_grade')
                                                            ->label('Performance Grade')
                                                            ->maxLength(255),
                                                        
                                                        Forms\Components\TextInput::make('modification_type')
                                                            ->label('Modification Type')
                                                            ->maxLength(255),
                                                        
                                                        Forms\Components\Select::make('unit')
                                                            ->label('Unit of Measurement')
                                                            ->options([
                                                                'ton' => 'Ton',
                                                                'barrel' => 'Barrel',
                                                            ])
                                                            ->required(),
                                                        
                                                        Forms\Components\Select::make('is_active')
                                                            ->label('Status')
                                                            ->options([
                                                                1 => 'Active',
                                                                0 => 'Inactive',
                                                            ])
                                                            ->default(1)
                                                            ->required(),
                                                    ])
                                                    ->columns(2),
                                                
                                                Forms\Components\Section::make('Pricing Information')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('price1')
                                                            ->label('Price 1')
                                                            ->numeric()
                                                            ->prefix('$'),
                                                        
                                                        Forms\Components\TextInput::make('price2')
                                                            ->label('Price 2')
                                                            ->numeric()
                                                            ->prefix('$'),
                                                    ])
                                                    ->columns(2),
                                            ])
                                            ->label('Product'),
                                        Forms\Components\TextInput::make('quantity')
                                            ->required()
                                            ->numeric()
                                            ->minValue(0.001)
                                            ->step(0.001)
                                            ->label('Quantity'),
                                        Forms\Components\TextInput::make('unit_price')
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->label('Unit Price'),
                                        Forms\Components\TextInput::make('tax_rate')
                                            ->numeric()
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->step(0.01)
                                            ->suffix('%')
                                            ->label('Tax Rate'),
                                    ]),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Add Product')
                            ->deleteAction(
                                fn (Action $action) => $action->label('Remove Product')
                            )
                            ->label('Products')
                            ->columnSpanFull(),
                    ])->columns(4),
                
                Forms\Components\Section::make('Order Summary')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Placeholder::make('subtotal')
                                    ->label('Subtotal')
                                    ->content(function (Get $get): string {
                                        $products = $get('receiptDocumentProducts') ?? [];
                                        $subtotal = 0;
                                        
                                        foreach ($products as $product) {
                                            if (isset($product['quantity']) && isset($product['unit_price'])) {
                                                $subtotal += $product['quantity'] * $product['unit_price'];
                                            }
                                        }
                                        
                                        return '$' . number_format($subtotal, 2);
                                    }),
                                
                                Forms\Components\Placeholder::make('tax_amount')
                                    ->label('Tax Amount')
                                    ->content(function (Get $get): string {
                                        $products = $get('receiptDocumentProducts') ?? [];
                                        $taxAmount = 0;
                                        
                                        foreach ($products as $product) {
                                            if (isset($product['quantity']) && isset($product['unit_price']) && isset($product['tax_rate'])) {
                                                $subtotal = $product['quantity'] * $product['unit_price'];
                                                $taxAmount += $subtotal * ($product['tax_rate'] / 100);
                                            }
                                        }
                                        
                                        return '$' . number_format($taxAmount, 2);
                                    }),
                                
                                Forms\Components\Placeholder::make('total')
                                    ->label('Total')
                                    ->content(function (Get $get): string {
                                        $products = $get('receiptDocumentProducts') ?? [];
                                        $total = 0;
                                        
                                        foreach ($products as $product) {
                                            if (isset($product['quantity']) && isset($product['unit_price'])) {
                                                $subtotal = $product['quantity'] * $product['unit_price'];
                                                $taxRate = $product['tax_rate'] ?? 0;
                                                $total += $subtotal * (1 + ($taxRate / 100));
                                            }
                                        }
                                        
                                        return '$' . number_format($total, 2);
                                    })
                                    ->extraAttributes(['class' => 'font-bold text-lg']),
                            ])
                    ])
                    ->collapsible()
                    ->collapsed(false),
                
                Forms\Components\Section::make('Officer Information')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('purchasing_officer_name')
                                        ->label('Purchasing Officer Name'),
                                    SignaturePad::make('purchasing_officer_signature')
                                        ->label('Purchasing Officer Signature')
                                        ->backgroundColor('rgb(245, 245, 245)')
                                        ->penColor('rgb(0, 0, 0)')
                                        ->exportPenColor('rgb(0, 0, 0)')
                                        ->exportBackgroundColor('rgb(255, 255, 255)')
                                        ->undoable()
                                        ->clearable()
                                        ->downloadable()
                                        ->downloadActionDropdownPlacement('center')
                                        ->confirmable(),
                                ]),
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('warehouse_officer_name')
                                        ->label('Warehouse Officer Name'),
                                    SignaturePad::make('warehouse_officer_signature')
                                        ->label('Warehouse Officer Signature')
                                        ->backgroundColor('rgb(245, 245, 245)')
                                        ->penColor('rgb(0, 0, 0)')
                                        ->exportPenColor('rgb(0, 0, 0)')
                                        ->exportBackgroundColor('rgb(255, 255, 255)')
                                        ->undoable()
                                        ->clearable()
                                        ->downloadable()
                                        ->downloadActionDropdownPlacement('center')
                                        ->confirmable(),
                                ]),
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('recipient_name')
                                        ->label('Recipient Name'),
                                    SignaturePad::make('recipient_signature')
                                        ->label('Recipient Signature')
                                        ->backgroundColor('rgb(245, 245, 245)')
                                        ->penColor('rgb(0, 0, 0)')
                                        ->exportPenColor('rgb(0, 0, 0)')
                                        ->exportBackgroundColor('rgb(255, 255, 255)')
                                        ->undoable()
                                        ->clearable()
                                        ->downloadable()
                                        ->downloadActionDropdownPlacement('center')
                                        ->confirmable(),
                                ]),
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('accountant_name')
                                        ->label('Accountant Name'),
                                    SignaturePad::make('accountant_signature')
                                        ->label('Accountant Signature')
                                        ->backgroundColor('rgb(245, 245, 245)')
                                        ->penColor('rgb(0, 0, 0)')
                                        ->exportPenColor('rgb(0, 0, 0)')
                                        ->exportBackgroundColor('rgb(255, 255, 255)')
                                        ->undoable()
                                        ->clearable()
                                        ->downloadable()
                                        ->downloadActionDropdownPlacement('center')
                                        ->confirmable(),
                                ]),
                            ]),
                    ])->collapsible(),
                
                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('note')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date_and_time')
                    ->dateTime()
                    ->sortable()
                    ->label('Date & Time'),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->sortable()
                    ->searchable()
                    ->label('Supplier'),
                Tables\Columns\TextColumn::make('transporter.name')
                    ->sortable()
                    ->searchable()
                    ->label('Transporter'),
                Tables\Columns\TextColumn::make('purchase_invoice_no')
                    ->searchable()
                    ->label('Invoice Number'),
                Tables\Columns\TextColumn::make('material_source')
                    ->searchable()
                    ->label('Material Source'),
                Tables\Columns\TextColumn::make('receiptDocumentProducts_count')
                    ->counts('receiptDocumentProducts')
                    ->label('Products Count')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('USD')
                    ->getStateUsing(function ($record) {
                        return $record->receiptDocumentProducts->sum(function ($product) {
                            return $product->total_with_tax ?? ($product->quantity * $product->unit_price * (1 + ($product->tax_rate / 100)));
                        });
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchasing_officer_name')
                    ->searchable()
                    ->toggledHiddenByDefault()
                    ->label('Purchasing Officer'),
                Tables\Columns\TextColumn::make('warehouse_officer_name')
                    ->searchable()
                    ->toggledHiddenByDefault()
                    ->label('Warehouse Officer'),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->searchable()
                    ->toggledHiddenByDefault()
                    ->label('Recipient'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggledHiddenByDefault(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('id_supplier')
                    ->relationship('supplier', 'name')
                    ->label('Supplier')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('id_transporter')
                    ->relationship('transporter', 'name')
                    ->label('Transporter')
                    ->searchable()
                    ->preload(),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Date From'),
                        DatePicker::make('date_until')
                            ->label('Date Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_and_time', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_and_time', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators['date_from'] = 'Date from ' . Carbon::parse($data['date_from'])->toFormattedDateString();
                        }
                        if ($data['date_until'] ?? null) {
                            $indicators['date_until'] = 'Date until ' . Carbon::parse($data['date_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('Export All')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')
                        ->label('Export Selected')
                        ->color('success')
                        ->icon('heroicon-o-arrow-down-tray'),
                ]),
            ])
            ->defaultSort('date_and_time', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            \App\Filament\Widgets\ReceiptDocumentStatsWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReceiptDocuments::route('/'),
            'create' => Pages\CreateReceiptDocument::route('/create'),
            'edit' => Pages\EditReceiptDocument::route('/{record}/edit'),
        ];
    }
}
