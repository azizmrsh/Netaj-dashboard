<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\DeliveryDocument;
use App\Models\ReceiptDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class RecentActivitiesWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getRecentActivitiesQuery())
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Activity Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Sales Invoice' => 'success',
                        'Purchase Invoice' => 'info',
                        'Delivery Document' => 'warning',
                        'Receipt Document' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('date', 'desc')
            ->paginated([5, 10, 25]);
    }

    protected function getRecentActivitiesQuery(): Builder
    {
        // Get recent sales invoices
        $salesInvoices = SalesInvoice::select([
            'id',
            'invoice_no as reference',
            'total_amount as amount',
            'created_at as date',
            'status'
        ])
        ->selectRaw("'Sales Invoice' as type")
        ->latest()
        ->limit(10);

        // Get recent purchase invoices
        $purchaseInvoices = PurchaseInvoice::select([
            'id',
            'invoice_no as reference',
            'total_amount_with_tax as amount',
            'created_at as date',
            'status'
        ])
        ->selectRaw("'Purchase Invoice' as type")
        ->latest()
        ->limit(10);

        // Get recent delivery documents
        $deliveryDocs = DeliveryDocument::select([
            'id',
            'document_number as reference',
            'total_amount as amount',
            'created_at as date'
        ])
        ->selectRaw("'Delivery Document' as type")
        ->selectRaw("'completed' as status")
        ->latest()
        ->limit(10);

        // Get recent receipt documents
        $receiptDocs = ReceiptDocument::select([
            'id',
            'document_number as reference',
            'total_amount as amount',
            'created_at as date'
        ])
        ->selectRaw("'Receipt Document' as type")
        ->selectRaw("'completed' as status")
        ->latest()
        ->limit(10);

        // Union all queries and return the combined result
        return $salesInvoices
            ->union($purchaseInvoices)
            ->union($deliveryDocs)
            ->union($receiptDocs);
    }
}
