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
    
    // Summary calculations
    public float $totalReceipts = 0;
    public float $totalIssues = 0;
    public float $finalBalance = 0;
    public float $totalAmountBeforeTax = 0;
    public float $vatAmount = 0;
    public float $totalAmountAfterTax = 0;
    public float $rate = 115; // Default rate as shown in blueprint

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
            'document_number' => 'OPENING BALANCE',
            'product_name' => 'Opening Balance',
            'receipts' => 0,
            'issues' => 0,
            'balance' => $runningBalance,
            'rate' => $this->rate,
            'value' => $runningBalance * $this->rate,
            'is_opening_balance' => true,
        ]);

        // Initialize totals
        $this->totalReceipts = 0;
        $this->totalIssues = 0;

        // Add transactions with running balance
        foreach ($transactions as $transaction) {
            $runningBalance += $transaction['receipts'] - $transaction['issues'];
            
            // Accumulate totals
            $this->totalReceipts += $transaction['receipts'];
            $this->totalIssues += $transaction['issues'];
            
            $reportData->push([
                'date' => $transaction['date'],
                'document_number' => $transaction['document_number'],
                'product_name' => $transaction['product_name'],
                'receipts' => $transaction['receipts'],
                'issues' => $transaction['issues'],
                'balance' => $runningBalance,
                'rate' => $this->rate,
                'value' => $runningBalance * $this->rate,
                'is_opening_balance' => false,
            ]);
        }

        // Calculate final balance and amounts
        $this->finalBalance = $runningBalance;
        $this->totalAmountBeforeTax = $this->finalBalance * $this->rate;
        $this->vatAmount = $this->totalAmountBeforeTax * 0.15; // 15% VAT
        $this->totalAmountAfterTax = $this->totalAmountBeforeTax + $this->vatAmount;

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
            ->with(['receiptDocumentProducts.product', 'transporter'])
            ->get();

        foreach ($receiptDocs as $doc) {
            $totalQty = $doc->receiptDocumentProducts->sum('quantity');
            $products = $doc->receiptDocumentProducts->pluck('product.name')->join(', ');
            
            $transactions->push([
                'date' => $doc->date_and_time,
                'document_number' => $doc->transporter->document_no ?? 'REC-' . $doc->id,
                'product_name' => "Receipt: {$products}",
                'receipts' => $totalQty,
                'issues' => 0,
                'sort_date' => $doc->date_and_time,
            ]);
        }

        // Get delivery documents in range
        $deliveryDocs = DeliveryDocument::where('id_customer', $customerId)
            ->whereBetween('date_and_time', [$dateFrom, $dateTo])
            ->with(['deliveryDocumentProducts.product', 'transporter'])
            ->get();

        foreach ($deliveryDocs as $doc) {
            $totalQty = $doc->deliveryDocumentProducts->sum('quantity');
            $products = $doc->deliveryDocumentProducts->pluck('product.name')->join(', ');
            
            $transactions->push([
                'date' => $doc->date_and_time,
                'document_number' => $doc->transporter->document_no ?? 'DEL-' . $doc->id,
                'product_name' => "Delivery: {$products}",
                'receipts' => 0,
                'issues' => $totalQty,
                'sort_date' => $doc->date_and_time,
            ]);
        }

        return $transactions->sortBy('sort_date')->values();
    }

    public function export()
    {
        $customer = $this->selectedCustomer;
        $dateFrom = $this->dateFrom;
        $dateTo = $this->dateTo;
        
        if (!$customer || !$dateFrom || !$dateTo) {
            Notification::make()
                ->title('خطأ')
                ->body('يرجى تحديد العميل والفترة الزمنية')
                ->danger()
                ->send();
            return;
        }

        $this->calculateReportData();
        $summaryData = $this->getSummaryData();
        
        $fileName = 'customer_report_' . $customer->name . '_' . $dateFrom . '_to_' . $dateTo . '.xlsx';
        
        return Excel::download(
            new CustomerReportExport($customer, $dateFrom, $dateTo, $this->reportData, $summaryData, $this->rate),
            $fileName
        );
    }

    public function getSummaryData(): Collection
    {
        return collect([
            [
                'date' => '',
                'document_number' => '',
                'product_name' => 'Total Receipts',
                'receipts' => $this->totalReceipts,
                'issues' => '',
                'balance' => '',
                'rate' => '',
                'value' => '',
                'is_summary' => true,
                'label' => 'Total Receipts'
            ],
            [
                'date' => '',
                'document_number' => '',
                'product_name' => 'Total of Issues',
                'receipts' => '',
                'issues' => $this->totalIssues,
                'balance' => '',
                'rate' => '',
                'value' => '',
                'is_summary' => true,
                'label' => 'Total of Issues'
            ],
            [
                'date' => '',
                'document_number' => '',
                'product_name' => 'Balance',
                'receipts' => '',
                'issues' => '',
                'balance' => $this->finalBalance,
                'rate' => '',
                'value' => '',
                'is_summary' => true,
                'label' => 'Balance'
            ],
            [
                'date' => '',
                'document_number' => '',
                'product_name' => 'Total Amount Before Tax',
                'receipts' => '',
                'issues' => '',
                'balance' => '',
                'rate' => '',
                'value' => $this->totalAmountBeforeTax,
                'is_summary' => true,
                'label' => 'Total Amount Before Tax'
            ],
            [
                'date' => '',
                'document_number' => '',
                'product_name' => 'Add VAT',
                'receipts' => '',
                'issues' => '',
                'balance' => '',
                'rate' => '',
                'value' => $this->vatAmount,
                'is_summary' => true,
                'label' => 'Add VAT'
            ],
            [
                'date' => '',
                'document_number' => '',
                'product_name' => 'Total Amount after Tax',
                'receipts' => '',
                'issues' => '',
                'balance' => '',
                'rate' => '',
                'value' => $this->totalAmountAfterTax,
                'is_summary' => true,
                'label' => 'Total Amount after Tax'
            ]
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}