<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Exports\CustomerReportExport;
use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\DeliveryDocument;
use App\Models\ReceiptDocument;
use App\Models\DeliveryDocumentProduct;
use App\Models\ReceiptDocumentProduct;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class CustomerReport extends Page implements Forms\Contracts\HasForms
{
    use InteractsWithForms;

    protected static string $resource = CustomerResource::class;
    protected static string $view = 'filament.resources.customer-resource.pages.customer-report';
    protected static ?string $title = 'Customer Report';

    public ?array $data = [];
    public bool $showResults = false;
    public ?Customer $selectedCustomer = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?float $openingBalance = 0;
    public Collection $reportData;

    public function mount(): void
    {
        // Pre-select customer if coming from customer table
        $customerId = request()->route('customer');
        if ($customerId) {
            $this->data['customer_id'] = $customerId;
        }
        
        $this->form->fill($this->data);
        $this->reportData = collect();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->label('Customer')
                    ->options(Customer::customers()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->selectedCustomer = $state ? Customer::find($state) : null;
                    }),
                
                Forms\Components\DatePicker::make('date_from')
                    ->label('From Date')
                    ->required()
                    ->default(now()->startOfMonth()),
                
                Forms\Components\DatePicker::make('date_to')
                    ->label('To Date')
                    ->required()
                    ->default(now()->endOfMonth()),
                
                Forms\Components\TextInput::make('opening_balance')
                    ->label('Opening Balance (الرصيد الافتتاحي)')
                    ->numeric()
                    ->default(0)
                    ->step(0.01)
                    ->suffix('units')
                    ->helperText('Enter the opening balance for the selected period'),
            ])
            ->statePath('data');
    }

    public function generateReport(): void
    {
        $data = $this->form->getState();
        
        $this->selectedCustomer = Customer::find($data['customer_id']);
        $this->dateFrom = $data['date_from'];
        $this->dateTo = $data['date_to'];
        $this->openingBalance = $data['opening_balance'] ?? 0;
        
        if (!$this->selectedCustomer) {
            Notification::make()
                ->title('Please select a customer')
                ->danger()
                ->send();
            return;
        }

        $this->calculateReportData();
        $this->showResults = true;
    }

    protected function calculateReportData(): void
    {
        $customerId = $this->selectedCustomer->id;
        $dateFrom = Carbon::parse($this->dateFrom);
        $dateTo = Carbon::parse($this->dateTo);

        // Use manual opening balance input
        $openingBalance = $this->openingBalance;

        // Get transactions within the date range
        $transactions = $this->getTransactionsInRange($customerId, $dateFrom, $dateTo);

        // Build report data with running balance
        $reportData = collect();
        $runningBalance = $openingBalance;

        // Add opening balance row
        $reportData->push([
            'date' => $dateFrom->format('Y-m-d'),
            'document_no' => 'OPENING BALANCE',
            'description' => 'Opening Balance',
            'receipts_qty' => 0,
            'issues_qty' => 0,
            'balance' => $runningBalance,
            'is_opening_balance' => true,
        ]);

        // Add transactions with running balance
        foreach ($transactions as $transaction) {
            $runningBalance += $transaction['receipts_qty'] - $transaction['issues_qty'];
            
            $reportData->push([
                'date' => $transaction['date'],
                'document_no' => $transaction['document_no'],
                'description' => $transaction['description'],
                'receipts_qty' => $transaction['receipts_qty'],
                'issues_qty' => $transaction['issues_qty'],
                'balance' => $runningBalance,
                'is_opening_balance' => false,
            ]);
        }

        $this->reportData = $reportData;
    }

    protected function calculateOpeningBalance(int $customerId, Carbon $beforeDate): float
    {
        // Sum all receipts before the date
        $totalReceipts = ReceiptDocumentProduct::whereHas('receiptDocument', function (Builder $query) use ($customerId, $beforeDate) {
            $query->where('id_customer', $customerId)
                  ->where('date_and_time', '<', $beforeDate);
        })->sum('quantity');

        // Sum all deliveries before the date
        $totalDeliveries = DeliveryDocumentProduct::whereHas('deliveryDocument', function (Builder $query) use ($customerId, $beforeDate) {
            $query->where('id_customer', $customerId)
                  ->where('date_and_time', '<', $beforeDate);
        })->sum('quantity');

        return $totalReceipts - $totalDeliveries;
    }

    protected function getTransactionsInRange(int $customerId, Carbon $dateFrom, Carbon $dateTo): Collection
    {
        $transactions = collect();

        // Get receipt documents in range
        $receiptDocs = ReceiptDocument::where('id_customer', $customerId)
            ->whereBetween('date_and_time', [$dateFrom, $dateTo])
            ->with(['receiptDocumentProducts.product'])
            ->get();

        foreach ($receiptDocs as $doc) {
            $totalQty = $doc->receiptDocumentProducts->sum('quantity');
            $products = $doc->receiptDocumentProducts->pluck('product.name')->join(', ');
            
            $transactions->push([
                'date' => $doc->date_and_time,
                'document_no' => $doc->document_number,
                'description' => "Receipt: {$products}",
                'receipts_qty' => $totalQty,
                'issues_qty' => 0,
                'sort_date' => $doc->date_and_time,
            ]);
        }

        // Get delivery documents in range
        $deliveryDocs = DeliveryDocument::where('id_customer', $customerId)
            ->whereBetween('date_and_time', [$dateFrom, $dateTo])
            ->with(['deliveryDocumentProducts.product'])
            ->get();

        foreach ($deliveryDocs as $doc) {
            $totalQty = $doc->deliveryDocumentProducts->sum('quantity');
            $products = $doc->deliveryDocumentProducts->pluck('product.name')->join(', ');
            
            $transactions->push([
                'date' => $doc->date_and_time,
                'document_no' => $doc->document_number,
                'description' => "Delivery: {$products}",
                'receipts_qty' => 0,
                'issues_qty' => $totalQty,
                'sort_date' => $doc->date_and_time,
            ]);
        }

        return $transactions->sortBy('sort_date')->values();
    }

    public function exportToExcel()
    {
        if (!$this->selectedCustomer || !$this->showResults) {
            Notification::make()
                ->title('يرجى إنشاء التقرير أولاً')
                ->danger()
                ->send();
            return;
        }

        try {
            $fileName = 'customer_report_' . $this->selectedCustomer->name . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            return Excel::download(
                new CustomerReportExport(
                    $this->selectedCustomer,
                    $this->dateFrom,
                    $this->dateTo,
                    $this->reportData
                ),
                $fileName
            );
        } catch (\Exception $e) {
            Notification::make()
                ->title('حدث خطأ أثناء تصدير التقرير')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}