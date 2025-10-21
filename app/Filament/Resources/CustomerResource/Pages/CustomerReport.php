<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Exports\CustomerReportExport;
use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\DeliveryDocument;
use App\Models\ReceiptDocument;
use App\Models\DeliveryDocumentProduct;
use App\Models\ReceiptDocumentProduct;
use App\Models\Product;
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

    public function __construct()
    {
        $this->reportData = collect();
    }
    
    // Summary calculations
    public float $totalReceipts = 0;
    public float $totalIssues = 0;
    public float $finalBalance = 0;
    public float $totalAmountBeforeTax = 0;
    public float $vatAmount = 0;
    public float $totalAmountAfterTax = 0;
    public float $unitRate = 115; // Unit price per ton (SAR)
    public float $taxRate = 15; // Tax rate percentage (%)

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
                    ->options(Customer::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->selectedCustomer = $state ? Customer::find($state) : null;
                    }),
                
                Forms\Components\Select::make('product_id')
                    ->label('Filter by Product - تصفية حسب المنتج (Optional)')
                    ->options(Product::pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->helperText('Leave empty to show all products'),
                
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
                
                Forms\Components\TextInput::make('unit_rate')
                    ->label('Unit Rate - سعر الوحدة (SAR/Ton)')
                    ->numeric()
                    ->default(115)
                    ->step(0.01)
                    ->required()
                    ->suffix('SAR')
                    ->helperText('Enter the unit price per ton (e.g., 115 SAR)'),
                
                Forms\Components\TextInput::make('tax_rate')
                    ->label('Tax Rate - معدل الضريبة (%)')
                    ->numeric()
                    ->default(15)
                    ->step(0.01)
                    ->suffix('%')
                    ->helperText('Enter the VAT percentage (e.g., 15 for 15%)'),
                
                Forms\Components\Toggle::make('separate_products')
                    ->label('Show products separately - عرض كل منتج في صف منفصل')
                    ->default(false)
                    ->inline(false)
                    ->helperText('When enabled, each product will be shown in a separate row (like Excel format)'),
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
        $this->unitRate = $data['unit_rate'] ?? 115; // Unit price per ton
        $this->taxRate = $data['tax_rate'] ?? 15; // Tax rate percentage
        
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
        $dateFrom = Carbon::parse($this->dateFrom)->startOfDay();
        $dateTo = Carbon::parse($this->dateTo)->endOfDay();

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
            'rate' => 0,
            'value' => 0,
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
            
            // Calculate value: only for issues (deliveries), not receipts
            $value = $transaction['issues'] > 0 ? $transaction['issues'] * $this->unitRate : 0;
            
            $reportData->push([
                'date' => $transaction['date'],
                'document_number' => $transaction['document_number'],
                'product_name' => $transaction['product_name'],
                'receipts' => $transaction['receipts'],
                'issues' => $transaction['issues'],
                'balance' => $runningBalance,
                'rate' => $transaction['issues'] > 0 ? $this->unitRate : 0,
                'value' => $value,
                'is_opening_balance' => false,
            ]);
        }

        // Calculate final balance and amounts
        $this->finalBalance = $runningBalance;
        
        // Total amount is sum of all delivery values (issues × unit rate)
        $this->totalAmountBeforeTax = $this->totalIssues * $this->unitRate;
        $this->vatAmount = $this->totalAmountBeforeTax * ($this->taxRate / 100); // Apply tax percentage
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
        $separateProducts = $this->data['separate_products'] ?? false;
        $productId = $this->data['product_id'] ?? null;

        // Get receipt documents in range
        $receiptQuery = ReceiptDocument::where('id_customer', $customerId)
            ->whereBetween('date_and_time', [$dateFrom, $dateTo])
            ->with(['receiptDocumentProducts.product']);
        
        if ($productId) {
            $receiptQuery->whereHas('receiptDocumentProducts', function($q) use ($productId) {
                $q->where('id_product', $productId);
            });
        }
        
        $receiptDocs = $receiptQuery->get();

        foreach ($receiptDocs as $doc) {
            $products = $doc->receiptDocumentProducts;
            
            // Filter products if specific product selected
            if ($productId) {
                $products = $products->where('id_product', $productId);
            }
            
            if ($separateProducts) {
                // Show each product in a separate row
                foreach ($products as $docProduct) {
                    $transactions->push([
                        'date' => $doc->date_and_time,
                        'document_number' => $doc->document_number ?: 'Receipt-' . $doc->id,
                        'product_name' => $docProduct->product->name,
                        'receipts' => $docProduct->quantity,
                        'issues' => 0,
                        'sort_date' => $doc->date_and_time,
                    ]);
                }
            } else {
                // Show all products in one row (current behavior)
                $totalQty = $products->sum('quantity');
                $productNames = $products->pluck('product.name')->join(', ');
                
                if ($totalQty > 0) {
                    $transactions->push([
                        'date' => $doc->date_and_time,
                        'document_number' => $doc->document_number ?: 'Receipt-' . $doc->id,
                        'product_name' => $productNames,
                        'receipts' => $totalQty,
                        'issues' => 0,
                        'sort_date' => $doc->date_and_time,
                    ]);
                }
            }
        }

        // Get delivery documents in range
        $deliveryQuery = DeliveryDocument::where('id_customer', $customerId)
            ->whereBetween('date_and_time', [$dateFrom, $dateTo])
            ->with(['deliveryDocumentProducts.product']);
        
        if ($productId) {
            $deliveryQuery->whereHas('deliveryDocumentProducts', function($q) use ($productId) {
                $q->where('id_product', $productId);
            });
        }
        
        $deliveryDocs = $deliveryQuery->get();

        foreach ($deliveryDocs as $doc) {
            $products = $doc->deliveryDocumentProducts;
            
            // Filter products if specific product selected
            if ($productId) {
                $products = $products->where('id_product', $productId);
            }
            
            if ($separateProducts) {
                // Show each product in a separate row
                foreach ($products as $docProduct) {
                    $transactions->push([
                        'date' => $doc->date_and_time,
                        'document_number' => $doc->document_number ?: 'Delivery-' . $doc->id,
                        'product_name' => $docProduct->product->name,
                        'receipts' => 0,
                        'issues' => $docProduct->quantity,
                        'sort_date' => $doc->date_and_time,
                    ]);
                }
            } else {
                // Show all products in one row (current behavior)
                $totalQty = $products->sum('quantity');
                $productNames = $products->pluck('product.name')->join(', ');
                
                if ($totalQty > 0) {
                    $transactions->push([
                        'date' => $doc->date_and_time,
                        'document_number' => $doc->document_number ?: 'Delivery-' . $doc->id,
                        'product_name' => $productNames,
                        'receipts' => 0,
                        'issues' => $totalQty,
                        'sort_date' => $doc->date_and_time,
                    ]);
                }
            }
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
            new CustomerReportExport($customer, $dateFrom, $dateTo, $this->reportData, $summaryData, $this->unitRate),
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