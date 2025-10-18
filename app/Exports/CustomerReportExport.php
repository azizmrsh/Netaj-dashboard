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
    protected $rate;

    public function __construct(Customer $customer, string $dateFrom, string $dateTo, $reportData, $summaryData = null, $rate = 0)
    {
        $this->customer = $customer;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->reportData = $reportData;
        $this->summaryData = $summaryData;
        $this->rate = $rate;
    }

    public function collection()
    {
        $data = collect($this->reportData);
        
        // Add summary data if provided
        if ($this->summaryData) {
            $data = $data->concat($this->summaryData);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
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
                '', // Date
                '', // Document No
                $row['label'] ?? '', // Product Name (used for summary labels)
                isset($row['receipts']) && is_numeric($row['receipts']) && $row['receipts'] > 0 ? number_format((float)$row['receipts'], 2) : '', // Receipts
                isset($row['issues']) && is_numeric($row['issues']) && $row['issues'] > 0 ? number_format((float)$row['issues'], 2) : '', // Issues
                isset($row['balance']) && is_numeric($row['balance']) && $row['balance'] > 0 ? number_format((float)$row['balance'], 2) : '', // Balance
                '', // Rate
                isset($row['value']) && is_numeric($row['value']) && $row['value'] > 0 ? number_format((float)$row['value'], 2) : '', // Value
            ];
        }

        // Regular data row
        return [
            isset($row['date']) ? Carbon::parse($row['date'])->format('d/m/Y') : '',
            $row['document_number'] ?? '',
            $row['product_name'] ?? '',
            is_numeric($row['receipts'] ?? 0) && ($row['receipts'] ?? 0) > 0 ? number_format((float)$row['receipts'], 2) : '',
            is_numeric($row['issues'] ?? 0) && ($row['issues'] ?? 0) > 0 ? number_format((float)$row['issues'], 2) : '',
            is_numeric($row['balance'] ?? 0) ? number_format((float)($row['balance'] ?? 0), 2) : '0.00',
            is_numeric($row['rate'] ?? 0) && ($row['rate'] ?? 0) > 0 ? number_format((float)$row['rate'], 0) : '',
            is_numeric($row['value'] ?? 0) && ($row['value'] ?? 0) > 0 ? number_format((float)$row['value'], 2) : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $headerRow = 1;

        // Add company header information at the top
        $sheet->insertNewRowBefore(1, 6);
        
        // Company name and title
        $sheet->setCellValue('A1', 'Inventory Account Statement - ' . $this->customer->name);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        // Date range
        $sheet->setCellValue('A2', 'From: ' . Carbon::parse($this->dateFrom)->format('d/m/Y') . ' To: ' . Carbon::parse($this->dateTo)->format('d/m/Y'));
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        // Report date
        $sheet->setCellValue('A3', 'Report Date: ' . Carbon::now()->format('d/m/Y'));
        $sheet->mergeCells('A3:H3');
        $sheet->getStyle('A3')->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        // Empty rows
        $sheet->setCellValue('A4', '');
        $sheet->setCellValue('A5', '');
        $sheet->setCellValue('A6', '');

        // Update header row position
        $headerRow = 7;
        $lastRow = $sheet->getHighestRow();

        // Style header row
        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray([
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
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders to all data cells
        $sheet->getStyle("A{$headerRow}:H{$lastRow}")->applyFromArray([
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

        // Style summary rows (assuming they are at the end)
        if ($this->summaryData && count($this->summaryData) > 0) {
            $summaryStartRow = $lastRow - (count($this->summaryData) - 1);
            $sheet->getStyle("A{$summaryStartRow}:H{$lastRow}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFF00'], // Yellow background
                ],
                'font' => [
                    'bold' => true,
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