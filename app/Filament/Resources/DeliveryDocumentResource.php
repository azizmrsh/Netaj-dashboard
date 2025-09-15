<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryDocumentResource\Pages;
use App\Filament\Resources\DeliveryDocumentResource\RelationManagers;
use App\Models\DeliveryDocument;
use App\Models\Customer;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliveryDocumentResource extends Resource
{
    protected static ?string $model = DeliveryDocument::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';

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
                                Forms\Components\Select::make('id_customer')
                                    ->relationship('customer', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->label('Customer Name'),
                                        Forms\Components\TextInput::make('phone')
                                            ->tel()
                                            ->label('Phone'),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->label('Email'),
                                        Forms\Components\Textarea::make('address')
                                            ->label('Address'),
                                    ])
                                    ->label('Customer'),
                                Forms\Components\Select::make('id_transporter')
                                    ->relationship('transporter', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->label('Transporter Name'),
                                        Forms\Components\TextInput::make('phone')
                                            ->tel()
                                            ->label('Phone'),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->label('Email'),
                                        Forms\Components\Textarea::make('address')
                                            ->label('Address'),
                                        Forms\Components\TextInput::make('license_number')
                                            ->label('License Number'),
                                    ])
                                    ->label('Transporter'),
                            ]),
                        Forms\Components\Repeater::make('deliveryDocumentProducts')
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
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->label('Product Name'),
                                                Forms\Components\Textarea::make('description')
                                                    ->label('Description'),
                                                Forms\Components\TextInput::make('unit')
                                                    ->label('Unit (e.g., kg, pieces)'),
                                                Forms\Components\TextInput::make('price')
                                                    ->numeric()
                                                    ->step(0.01)
                                                    ->label('Default Price'),
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
                        Forms\Components\Placeholder::make('subtotal')
                            ->label('Subtotal (Before Tax)')
                            ->content(function (Get $get): string {
                                $products = $get('deliveryDocumentProducts') ?? [];
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
                                $products = $get('deliveryDocumentProducts') ?? [];
                                $taxAmount = 0;
                                
                                foreach ($products as $product) {
                                    if (isset($product['quantity']) && isset($product['unit_price']) && isset($product['tax_rate'])) {
                                        $lineTotal = $product['quantity'] * $product['unit_price'];
                                        $taxAmount += $lineTotal * ($product['tax_rate'] / 100);
                                    }
                                }
                                
                                return '$' . number_format($taxAmount, 2);
                            }),
                        Forms\Components\Placeholder::make('total')
                            ->label('Total (After Tax)')
                            ->content(function (Get $get): string {
                                $products = $get('deliveryDocumentProducts') ?? [];
                                $subtotal = 0;
                                $taxAmount = 0;
                                
                                foreach ($products as $product) {
                                    if (isset($product['quantity']) && isset($product['unit_price'])) {
                                        $lineTotal = $product['quantity'] * $product['unit_price'];
                                        $subtotal += $lineTotal;
                                        
                                        if (isset($product['tax_rate'])) {
                                            $taxAmount += $lineTotal * ($product['tax_rate'] / 100);
                                        }
                                    }
                                }
                                
                                $total = $subtotal + $taxAmount;
                                return '$' . number_format($total, 2);
                            }),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('purchase_order_no')
                                    ->required()
                                    ->label('Purchase Order Number'),
                                Forms\Components\TextInput::make('project_name_and_location')
                                    ->required()
                                    ->label('Project Name and Location'),
                            ]),
                    ])->columns(2),
                
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
                Tables\Columns\TextColumn::make('customer.name')
                    ->sortable()
                    ->searchable()
                    ->label('Customer'),
                Tables\Columns\TextColumn::make('transporter.name')
                    ->sortable()
                    ->searchable()
                    ->label('Transporter'),
                Tables\Columns\TextColumn::make('deliveryDocumentProducts')
                    ->label('Products')
                    ->formatStateUsing(function ($record) {
                        return $record->deliveryDocumentProducts
                            ->map(function($item) {
                                $text = $item->product->name . ' (Qty: ' . $item->quantity;
                                if ($item->unit_price) {
                                    $text .= ', Price: $' . number_format($item->unit_price, 2);
                                }
                                if ($item->tax_rate) {
                                    $text .= ', Tax: ' . $item->tax_rate . '%';
                                }
                                $text .= ')';
                                return $text;
                            })
                            ->join(', ');
                    })
                    ->searchable(false)
                    ->sortable(false)
                    ->limit(100),
                Tables\Columns\TextColumn::make('purchase_order_no')
                    ->searchable()
                    ->label('PO Number'),
                Tables\Columns\TextColumn::make('project_name_and_location')
                    ->searchable()
                    ->limit(50)
                    ->label('Project'),
                Tables\Columns\TextColumn::make('purchasing_officer_name')
                    ->searchable()
                    ->toggleable()
                    ->label('Purchasing Officer'),
                Tables\Columns\TextColumn::make('warehouse_officer_name')
                    ->searchable()
                    ->toggleable()
                    ->label('Warehouse Officer'),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->searchable()
                    ->toggleable()
                    ->label('Recipient'),
                Tables\Columns\TextColumn::make('accountant_name')
                    ->searchable()
                    ->toggleable()
                    ->label('Accountant'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('date_and_time')
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
                    }),
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
                SelectFilter::make('id_customer')
                    ->relationship('customer', 'name')
                    ->label('Customer')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('id_transporter')
                    ->relationship('transporter', 'name')
                    ->label('Transporter')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                Filter::make('purchase_order_no')
                    ->form([
                        TextInput::make('purchase_order_no')
                            ->label('Purchase Order Number')
                            ->placeholder('Search by PO number...'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['purchase_order_no'],
                                fn (Builder $query, $po): Builder => $query->where('purchase_order_no', 'like', "%{$po}%"),
                            );
                    }),
                Filter::make('project_name_and_location')
                    ->form([
                        TextInput::make('project_name')
                            ->label('Project Name')
                            ->placeholder('Search by project name...'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['project_name'],
                                fn (Builder $query, $project): Builder => $query->where('project_name_and_location', 'like', "%{$project}%"),
                            );
                    }),
                Filter::make('officers')
                    ->form([
                        TextInput::make('purchasing_officer')
                            ->label('Purchasing Officer')
                            ->placeholder('Search by purchasing officer...'),
                        TextInput::make('warehouse_officer')
                            ->label('Warehouse Officer')
                            ->placeholder('Search by warehouse officer...'),
                        TextInput::make('recipient')
                            ->label('Recipient')
                            ->placeholder('Search by recipient...'),
                        TextInput::make('accountant')
                            ->label('Accountant')
                            ->placeholder('Search by accountant...'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['purchasing_officer'],
                                fn (Builder $query, $officer): Builder => $query->where('purchasing_officer_name', 'like', "%{$officer}%"),
                            )
                            ->when(
                                $data['warehouse_officer'],
                                fn (Builder $query, $officer): Builder => $query->where('warehouse_officer_name', 'like', "%{$officer}%"),
                            )
                            ->when(
                                $data['recipient'],
                                fn (Builder $query, $recipient): Builder => $query->where('recipient_name', 'like', "%{$recipient}%"),
                            )
                            ->when(
                                $data['accountant'],
                                fn (Builder $query, $accountant): Builder => $query->where('accountant_name', 'like', "%{$accountant}%"),
                            );
                    }),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)

            ->filtersFormColumns(4)
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->fileName('Delivery Documents')
                    ->timeFormat('Y-m-d_H-i-s')
                    ->defaultFormat('xlsx')
                    ->withColumns([
                        Tables\Columns\TextColumn::make('date_and_time')->label('Date & Time'),
                        Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
                        Tables\Columns\TextColumn::make('transporter.name')->label('Transporter'),
                        Tables\Columns\TextColumn::make('purchase_order_no')->label('PO Number'),
                        Tables\Columns\TextColumn::make('project_name_and_location')->label('Project'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')
                        ->fileName('Selected Delivery Documents')
                        ->timeFormat('Y-m-d_H-i-s')
                        ->defaultFormat('xlsx'),
                ]),
            ]);
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
            'index' => Pages\ListDeliveryDocuments::route('/'),
            'create' => Pages\CreateDeliveryDocument::route('/create'),
            'edit' => Pages\EditDeliveryDocument::route('/{record}/edit'),
        ];
    }
}
