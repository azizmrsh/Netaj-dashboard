<?php

namespace App\Filament\Resources\SalesInvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Get;
use Filament\Forms\Set;

class DeliveryDocumentProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveryDocumentProducts';

    protected static ?string $title = 'Products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Information')
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        TextInput::make('quantity')
                            ->label('Delivered Quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => $this->calculateTotals($set, $get)),
                    ])
                    ->columns(2),
                
                Section::make('Pricing Information')
                    ->schema([
                        TextInput::make('unit_price')
                            ->label('Unit Price')
                            ->required()
                            ->numeric()
                            ->prefix('SAR')
                            ->minValue(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => $this->calculateTotals($set, $get)),
                        
                        TextInput::make('tax_rate')
                            ->label('Tax Rate (%)')
                            ->required()
                            ->numeric()
                            ->default(15.00)
                            ->suffix('%')
                            ->minValue(0)
                            ->maxValue(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => $this->calculateTotals($set, $get)),
                        
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
                    ])
                    ->columns(2),
            ]);
    }
    
    private function calculateTotals(Set $set, Get $get): void
    {
        $quantity = (float) $get('quantity') ?: 0;
        $unitPrice = (float) $get('unit_price') ?: 0;
        $taxRate = (float) $get('tax_rate') ?: 0;
        
        $taxAmount = ($unitPrice * $taxRate) / 100;
        $unitPriceWithTax = $unitPrice + $taxAmount;
        $subtotal = $quantity * $unitPrice;
        $totalTax = $quantity * $taxAmount;
        $totalWithTax = $quantity * $unitPriceWithTax;
        
        $set('tax_amount', number_format($taxAmount, 2, '.', ''));
        $set('unit_price_with_tax', number_format($unitPriceWithTax, 2, '.', ''));
        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('total_tax', number_format($totalTax, 2, '.', ''));
        $set('total_with_tax', number_format($totalWithTax, 2, '.', ''));
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('quantity')
                    ->label('Delivered Quantity')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('product.unit')
                    ->label('Unit')
                    ->sortable(),
                
                TextColumn::make('unit_price')
                    ->label('Unit Price')
                    ->money('SAR')
                    ->sortable(),
                
                TextColumn::make('tax_rate')
                    ->label('Tax Rate')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->sortable(),
                
                TextColumn::make('total_with_tax')
                    ->label('Total with Tax')
                    ->money('SAR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Remove create action as products should be managed through delivery documents
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Pricing'),
            ])
            ->bulkActions([
                // Remove bulk actions
            ])
            ->emptyStateHeading('No Products Found')
            ->emptyStateDescription('Products will appear here once they are added to the delivery document.')
            ->paginated([10, 25, 50]);
    }
}
