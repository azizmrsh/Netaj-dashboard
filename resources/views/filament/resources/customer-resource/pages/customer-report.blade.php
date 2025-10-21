<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Report Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Report Filters</h3>
            
            <form wire:submit="generateReport">
                {{ $this->form }}
                
                <div class="mt-6">
                    <x-filament::button type="submit" size="lg">
                        <x-heroicon-o-document-chart-bar class="w-5 h-5 mr-2" />
                        Generate Report
                    </x-filament::button>
                </div>
            </form>
        </div>

        <!-- Report Results -->
        @if($showResults)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Inventory Account Statement - {{ $selectedCustomer?->name }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Period: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} to {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                            Unit Rate: {{ number_format($unitRate, 2) }} SAR/Ton | Tax Rate: {{ number_format($taxRate, 2) }}%
                        </p>
                    </div>
                    
                    <div class="flex space-x-3">
                        <x-filament::button 
                            wire:click="export" 
                            color="success" 
                            outlined
                            class="no-print"
                        >
                            <x-heroicon-o-document-arrow-down class="w-4 h-4 mr-2" />
                            Export to Excel
                        </x-filament::button>
                        
                        <x-filament::button 
                            onclick="window.print()" 
                            color="gray" 
                            outlined
                            class="no-print"
                        >
                            <x-heroicon-o-printer class="w-4 h-4 mr-2" />
                            Print
                        </x-filament::button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Document No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Product Name
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Receipts<br><span class="text-xs font-normal">(Quantity Ton)</span>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Issues<br><span class="text-xs font-normal">(Quantity Ton)</span>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Balance<br><span class="text-xs font-normal">(Quantity Ton)</span>
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Rate (SAR)
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Value (SAR)
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($reportData as $index => $row)
                        <tr class="{{ $row['is_opening_balance'] ? 'bg-blue-50 dark:bg-blue-900/20 font-bold' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $row['is_opening_balance'] ? '*' : $index }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $row['is_opening_balance'] ? '*' : \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $row['is_opening_balance'] ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-gray-100' }}">
                                {{ $row['document_number'] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                {{ $row['product_name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                {{ $row['receipts'] > 0 ? number_format($row['receipts'], 2) : ($row['is_opening_balance'] ? '*' : '') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                {{ $row['issues'] > 0 ? number_format($row['issues'], 2) : ($row['is_opening_balance'] ? '*' : '') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right {{ $row['balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ number_format($row['balance'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                {{ $row['rate'] > 0 ? number_format($row['rate'], 2) : ($row['is_opening_balance'] ? '*' : '') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 text-right">
                                {{ $row['value'] > 0 ? number_format($row['value'], 2) : ($row['is_opening_balance'] ? '*' : '') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No data found for the selected period.
                            </td>
                        </tr>
                        @endforelse
                        
                        <!-- Summary Section - Row 1: Total Receipts, Total of Issues, Balance -->
                        @if($reportData->isNotEmpty())
                        <tr class="bg-yellow-100 dark:bg-yellow-900/30 border-t-4 border-yellow-400 dark:border-yellow-500">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">Total Receipts</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-right">
                                {{ number_format($totalReceipts, 2) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">Total Amount Before Tax</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-right">
                                {{ number_format($totalAmountBeforeTax, 2) }}
                            </td>
                        </tr>
                        
                        <!-- Summary Section - Row 2: Total of Issues, Add VAT -->
                        <tr class="bg-yellow-100 dark:bg-yellow-900/30">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">Total of Issues</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-right">
                                {{ number_format($totalIssues, 2) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">Add VAT</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-right">
                                {{ number_format($vatAmount, 2) }}
                            </td>
                        </tr>
                        
                        <!-- Summary Section - Row 3: Balance, Total Amount after Tax -->
                        <tr class="bg-yellow-100 dark:bg-yellow-900/30 border-b-4 border-yellow-400 dark:border-yellow-500">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">Balance</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">*</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold {{ $finalBalance >= 0 ? 'text-green-600' : 'text-red-600' }} text-right">
                                {{ number_format($finalBalance, 2) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-center">Total Amount after Tax</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-gray-100 text-right">
                                {{ number_format($totalAmountAfterTax, 2) }}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                font-size: 12px;
            }
            
            .bg-white {
                background: white !important;
            }
            
            .text-gray-900 {
                color: black !important;
            }
            
            .shadow {
                box-shadow: none !important;
            }
            
            .rounded-lg {
                border-radius: 0 !important;
            }
            
            table {
                border-collapse: collapse;
                width: 100%;
            }
            
            th, td {
                border: 1px solid #000;
                padding: 8px;
            }
            
            thead {
                background: #f5f5f5 !important;
            }
        }
    </style>
</x-filament-panels::page>
