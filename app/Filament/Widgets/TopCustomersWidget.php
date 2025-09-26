<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\SalesInvoice;
use Illuminate\Database\Eloquent\Builder;

class TopCustomersWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Customers by Sales';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'md';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SalesInvoice::query()
                    ->selectRaw('
                        customer_name,
                        customer_phone,
                        customer_tax_number,
                        SUM(total_amount) as total_sales,
                        COUNT(*) as invoice_count,
                        AVG(total_amount) as avg_order_value
                    ')
                    ->groupBy('customer_name', 'customer_phone', 'customer_tax_number')
                    ->orderByDesc('total_sales')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_sales')
                    ->label('Total Sales')
                    ->money('SAR')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),
                Tables\Columns\TextColumn::make('invoice_count')
                    ->label('Orders')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('avg_order_value')
                    ->label('Avg Order')
                    ->money('SAR')
                    ->alignEnd()
                    ->color('info'),
                Tables\Columns\TextColumn::make('customer_tax_number')
                    ->label('Tax Number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('total_sales', 'desc')
            ->paginated(false);
    }
}
