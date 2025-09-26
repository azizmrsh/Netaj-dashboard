<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\DeliveryDocumentProduct;
use Illuminate\Database\Eloquent\Builder;

class TopProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Selling Products';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'md';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DeliveryDocumentProduct::query()
                    ->selectRaw('
                        product_id,
                        SUM(quantity) as total_quantity,
                        SUM(total_with_tax) as total_sales,
                        COUNT(*) as sales_count
                    ')
                    ->with('product')
                    ->groupBy('product_id')
                    ->orderByDesc('total_quantity')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('product.product_code')
                    ->label('Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Total Sold')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('total_sales')
                    ->label('Total Revenue')
                    ->money('SAR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('sales_count')
                    ->label('Orders')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('product.unit')
                    ->label('Unit')
                    ->alignCenter(),
            ])
            ->defaultSort('total_quantity', 'desc')
            ->paginated(false);
    }
}
