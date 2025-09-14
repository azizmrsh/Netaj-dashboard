<?php

namespace App\Filament\Resources\PurchaseInvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReceiptDocumentProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'receiptDocumentProducts';
    
    protected static ?string $title = 'Receipt Document Products';
    
    protected static ?string $modelLabel = 'Product';
    
    protected static ?string $pluralModelLabel = 'Products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Pricing Information')
                    ->description('Set unit price and tax for the product')
                    ->schema([
                        TextInput::make('unit_price')
                            ->label('Unit Price')
                            ->required()
                            ->numeric()
                            ->prefix('SAR')
                            ->minValue(0)
                            ->step(0.01)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $this->calculateTotals($state, $set, $get);
                            }),
                        TextInput::make('tax_rate')
                            ->label('Tax Rate (%)')
                            ->required()
                            ->numeric()
                            ->suffix('%')
                            ->default(15.00)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $this->calculateTotals($state, $set, $get);
                            }),
                        TextInput::make('tax_amount')
                            ->label('Tax Amount')
                            ->numeric()
                            ->prefix('SAR')
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('unit_price_with_tax')
                            ->label('Unit Price with Tax')
                            ->numeric()
                            ->prefix('SAR')
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('SAR')
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('total_tax')
                            ->label('Total Tax')
                            ->numeric()
                            ->prefix('SAR')
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('total_with_tax')
                            ->label('Total with Tax')
                            ->numeric()
                            ->prefix('SAR')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),
            ]);
    }
    
    private function calculateTotals($state, $set, $get)
    {
        $unitPrice = (float) ($get('unit_price') ?? 0);
        $taxRate = (float) ($get('tax_rate') ?? 0);
        $quantity = (float) ($get('quantity') ?? 1);
        
        $taxAmount = ($unitPrice * $taxRate) / 100;
        $unitPriceWithTax = $unitPrice + $taxAmount;
        $subtotal = $unitPrice * $quantity;
        $totalTax = $taxAmount * $quantity;
        $totalWithTax = $unitPriceWithTax * $quantity;
        
        $set('tax_amount', round($taxAmount, 4));
        $set('unit_price_with_tax', round($unitPriceWithTax, 2));
        $set('subtotal', round($subtotal, 2));
        $set('total_tax', round($totalTax, 2));
        $set('total_with_tax', round($totalWithTax, 2));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label('Unit Price')
                    ->money('SAR')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('tax_rate')
                    ->label('Tax Rate')
                    ->formatStateUsing(fn (string $state): string => $state . '%')
                    ->alignCenter(),
                TextColumn::make('total_with_tax')
                    ->label('Total')
                    ->money('SAR')
                    ->alignEnd()
                    ->weight(FontWeight::Bold)
                    ->color('success'),
                TextColumn::make('product.unit')
                    ->label('Unit')
                    ->alignCenter()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Remove create action as products come from receipt document
            ])
            ->actions([
                EditAction::make()
                    ->label('Set Price')
                    ->modalHeading('Set Product Price and Tax'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Remove delete action as products are linked to receipt document
                ]),
            ])
            ->emptyStateHeading('No Products Found')
            ->emptyStateDescription('Products are automatically fetched from the selected receipt document')
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
