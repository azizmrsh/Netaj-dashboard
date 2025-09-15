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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
                                Forms\Components\Grid::make(2)
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
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
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
                        Forms\Components\TextInput::make('purchase_order_no')
                            ->required()
                            ->label('Purchase Order Number'),
                        Forms\Components\Textarea::make('project_name_and_location')
                            ->required()
                            ->label('Project Name and Location')
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Officer Information')
                    ->schema([
                        Forms\Components\TextInput::make('purchasing_officer_name')
                            ->label('Purchasing Officer Name'),
                        Forms\Components\Textarea::make('purchasing_officer_signature')
                            ->label('Purchasing Officer Signature'),
                        Forms\Components\TextInput::make('warehouse_officer_name')
                            ->label('Warehouse Officer Name'),
                        Forms\Components\Textarea::make('warehouse_officer_signature')
                            ->label('Warehouse Officer Signature'),
                        Forms\Components\TextInput::make('recipient_name')
                            ->label('Recipient Name'),
                        Forms\Components\Textarea::make('recipient_signature')
                            ->label('Recipient Signature'),
                        Forms\Components\TextInput::make('accountant_name')
                            ->label('Accountant Name'),
                        Forms\Components\Textarea::make('accountant_signature')
                            ->label('Accountant Signature'),
                    ])->columns(2)->collapsible(),
                
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
