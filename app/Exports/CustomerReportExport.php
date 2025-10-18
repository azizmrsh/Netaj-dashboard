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

    public function __construct(Customer $customer, string $dateFrom, string $dateTo, $reportData, $summaryData = null, $rate = 115)
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
            'التاريخ',
            'رقم المستند',
            'اسم المنتج',
            'الوارد (كمية)',
            'الصادر (كمية)',
            'الرصيد',
            'السعر (ريال)',
            'القيمة (ريال)',
        ];
    }

    public function map($row): array
    {
        // Check if this is a summary row
        if (isset($row['is_summary']) && $row['is_summary']) {
            return [
                $row['date'] ?? '',
                $row['document_no'] ?? '',
                $row['description'] ?? '',
                is_numeric($row['receipts_qty']) ? $row['receipts_qty'] : $row['receipts_qty'],
                is_numeric($row['issues_qty']) ? $row['issues_qty'] : $row['issues_qty'],
                is_numeric($row['balance']) ? $row['balance'] : $row['balance'],
                $row['rate'] ?? '',
                is_numeric($row['value']) ? $row['value'] : $row['value'],
            ];
        }
        
        // Regular data row
        return [
            $row['date'] ? Carbon::parse($row['date'])->format('Y-m-d') : '',
            $row['document_no'] ?? '',
            $row['description'] ?? '',
            $row['receipts_qty'] ?? 0,
            $row['issues_qty'] ?? 0,
            $row['balance'] ?? 0,
            $row['is_opening_balance'] ? '' : $this->rate,
            $row['is_opening_balance'] ? '' : ($row['balance'] * $this->rate),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set RTL direction for Arabic text
        $sheet->setRightToLeft(true);
        
        // Get the last row number
        $lastRow = $sheet->getHighestRow();
        $headerRow = 4; // After the info rows
        
        // Style the header row
        $sheet->getStyle("A{$headerRow}:H{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'],
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
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style summary rows (last 3 rows) with yellow background
        if ($this->summaryData && count($this->summaryData) > 0) {
            $summaryStartRow = $lastRow - count($this->summaryData) + 1;
            $sheet->getStyle("A{$summaryStartRow}:H{$lastRow}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FEF3C7'], // Light yellow
                ],
                'font' => [
                    'bold' => true,
                ],
            ]);
        }

        // Add customer info at the top
        $sheet->insertNewRowBefore(1, 3);
        $sheet->setCellValue('A1', 'تقرير العميل: ' . $this->customer->name);
        $sheet->setCellValue('A2', 'الفترة: من ' . $this->dateFrom . ' إلى ' . $this->dateTo);
        $sheet->setCellValue('A3', 'تاريخ التقرير: ' . now()->format('Y-m-d H:i'));

        // Style the info rows
        $sheet->getStyle('A1:H3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        // Merge cells for info rows
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');

        return $sheet;
    }

    public function title(): string
    {
        return 'تقرير العميل - ' . $this->customer->name;
    }
}