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
                        Forms\Components\DateTimePicker::make('date_and_time')
                            ->required()
                            
                            ->label('Date and Time'),
                        Forms\Components\Select::make('id_customer')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Customer'),
                        Forms\Components\Select::make('id_transporter')
                            ->relationship('transporter', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Transporter'),
                        Forms\Components\Repeater::make('deliveryDocumentProducts')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Product')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0.001)
                                    ->step(0.001)
                                    ->label('Quantity')
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add Product')
                            ->deleteAction(
                                fn (Action $action) => $action->label('Remove Product')
                            )
                            ->label('Products')
                            ->columnSpanFull(),
                    ])->columns(2),
                
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
                            ->map(fn($item) => $item->product->name . ' (' . $item->quantity . ')')
                            ->join(', ');
                    })
                    ->searchable(false)
                    ->sortable(false)
                    ->limit(50),
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
