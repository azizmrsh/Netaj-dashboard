<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\DeliveryDocument;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomerReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $customer;
    protected $dateFrom;
    protected $dateTo;
    protected $reportData;
    protected $summaryData;
    protected $unitRate;

    public function __construct(Customer $customer, string $dateFrom, string $dateTo, $reportData, $summaryData = null, $unitRate = 115)
    {
        $this->customer = $customer;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->reportData = $reportData;
        $this->summaryData = $summaryData;
        $this->unitRate = $unitRate;
    }

    public function collection()
    {
        $data = collect($this->reportData);
        
        // Add summary data as 3 horizontal rows (matching Excel format)
        if ($this->summaryData) {
            // Row 1: Total Receipts + Total Amount Before Tax (في صف واحد أفقي)
            $data->push([
                'date' => '*',
                'document_number' => '*',
                'product_name' => '*',
                'receipts_label' => 'Total Receipts',
                'receipts' => $this->summaryData[0]['receipts'],
                'issues' => '*',
                'balance' => '*',
                'rate_label' => 'Total Amount Before Tax',
                'value' => $this->summaryData[3]['value'],
                'is_summary' => true,
            ]);
            
            // Row 2: Total of Issues + Add VAT (في صف واحد أفقي)
            $data->push([
                'date' => '*',
                'document_number' => '*',
                'product_name' => '*',
                'receipts_label' => 'Total of Issues',
                'receipts' => '*',
                'issues' => $this->summaryData[1]['issues'],
                'balance' => '*',
                'rate_label' => 'Add VAT',
                'value' => $this->summaryData[4]['value'],
                'is_summary' => true,
            ]);
            
            // Row 3: Balance + Total Amount after Tax (في صف واحد أفقي)
            $data->push([
                'date' => '*',
                'document_number' => '*',
                'product_name' => '*',
                'receipts_label' => 'Balance',
                'receipts' => '*',
                'issues' => '*',
                'balance' => $this->summaryData[2]['balance'],
                'rate_label' => 'Total Amount after Tax',
                'value' => $this->summaryData[5]['value'],
                'is_summary' => true,
            ]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Date',
            'Document No',
            'Product Name',
            'Receipts(in)',
            'Issues(out)',
            'Balance',
            'Rate (SAR)',
            'Value (SAR)',
        ];
    }

    public function map($row): array
    {
        // Check if this is a summary row
        if (isset($row['is_summary']) && $row['is_summary']) {
            return [
                '*', // No
                '*', // Date
                '*', // Document No
                $row['receipts_label'] ?? '', // Product Name: "Total Receipts" etc
                is_numeric($row['receipts'] ?? null) && $row['receipts'] != '*' ? number_format((float)$row['receipts'], 2) : '*',
                is_numeric($row['issues'] ?? null) && $row['issues'] != '*' ? number_format((float)$row['issues'], 2) : '*',
                is_numeric($row['balance'] ?? null) && $row['balance'] != '*' ? number_format((float)$row['balance'], 2) : '*',
                $row['rate_label'] ?? '', // Rate: "Total Amount Before Tax" etc
                is_numeric($row['value'] ?? null) && $row['value'] != '*' ? number_format((float)$row['value'], 2) : '*',
            ];
        }

        // Regular data row
        static $rowNumber = 0;
        $rowNumber++;
        
        // Check if this is opening balance
        $isOpeningBalance = isset($row['is_opening_balance']) && $row['is_opening_balance'];
        
        return [
            $isOpeningBalance ? '*' : $rowNumber,
            $isOpeningBalance ? '*' : (isset($row['date']) ? Carbon::parse($row['date'])->format('d/m/Y') : ''),
            $row['document_number'] ?? '',
            $row['product_name'] ?? '',
            is_numeric($row['receipts'] ?? 0) && ($row['receipts'] ?? 0) > 0 ? number_format((float)$row['receipts'], 2) : ($isOpeningBalance ? '*' : ''),
            is_numeric($row['issues'] ?? 0) && ($row['issues'] ?? 0) > 0 ? number_format((float)$row['issues'], 2) : ($isOpeningBalance ? '*' : ''),
            is_numeric($row['balance'] ?? 0) ? number_format((float)($row['balance'] ?? 0), 2) : '0.00',
            is_numeric($row['rate'] ?? 0) && ($row['rate'] ?? 0) > 0 ? number_format((float)$row['rate'], 0) : ($isOpeningBalance ? '*' : ''),
            is_numeric($row['value'] ?? 0) && ($row['value'] ?? 0) > 0 ? number_format((float)$row['value'], 2) : ($isOpeningBalance ? '*' : ''),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $headerRow = 1;

        // Add company header information at the top (matching Excel format)
        $sheet->insertNewRowBefore(1, 7);
        
        // Company name
        $sheet->setCellValue('A1', 'Netaj Almotatwrah Commercial Company');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        // Product/Report name (Bin Card format)
        $productInfo = 'Inventory Account Statement - Bin Card';
        $sheet->setCellValue('A2', $productInfo);
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        // Date range
        $sheet->setCellValue('A3', 'From ' . Carbon::parse($this->dateFrom)->format('d-m-Y') . ' To ' . Carbon::parse($this->dateTo)->format('d-m-Y'));
        $sheet->mergeCells('A3:I3');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        // Customer name
        $sheet->setCellValue('A4', 'Customer Name: ' . $this->customer->name);
        $sheet->mergeCells('A4:I4');
        $sheet->getStyle('A4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        // Empty row
        $sheet->setCellValue('A5', '');
        
        // Sub-header row (Quantity Ton labels)
        $sheet->setCellValue('A6', '');
        $sheet->setCellValue('B6', '');
        $sheet->setCellValue('C6', '');
        $sheet->setCellValue('D6', '');
        $sheet->setCellValue('E6', 'Quantity Ton');
        $sheet->setCellValue('F6', 'Quantity Ton');
        $sheet->setCellValue('G6', 'Quantity Ton');
        $sheet->setCellValue('H6', '');
        $sheet->setCellValue('I6', '');
        
        $sheet->getStyle('A6:I6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E7E6E6'],
            ],
        ]);
        
        // Empty row before headers
        $sheet->setCellValue('A7', '');

        // Update header row position
        $headerRow = 8;
        $lastRow = $sheet->getHighestRow();

        // Style header row
        $sheet->getStyle("A{$headerRow}:I{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Auto-size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders to all data cells
        $sheet->getStyle("A{$headerRow}:I{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Style summary rows (last 3 rows with yellow background)
        if ($this->summaryData && count($this->summaryData) > 0) {
            $summaryStartRow = $lastRow - 2; // Last 3 rows
            $sheet->getStyle("A{$summaryStartRow}:I{$lastRow}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFF00'], // Bright yellow like Excel
                ],
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                    'outline' => [
                        'borderStyle' => Border::BORDER_MEDIUM,
                        'color' => ['rgb' => 'FF9900'], // Orange outline
                    ],
                ],
            ]);
        }

        return $sheet;
    }

    public function title(): string
    {
        return 'Customer Report - ' . $this->customer->name;
    }
}