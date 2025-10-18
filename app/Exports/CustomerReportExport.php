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

    public function __construct(Customer $customer, string $dateFrom, string $dateTo, $reportData)
    {
        $this->customer = $customer;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->reportData = $reportData;
    }

    public function collection()
    {
        return collect($this->reportData);
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'رقم المستند',
            'الوصف',
            'الوارد (كمية)',
            'الصادر (كمية)',
            'الرصيد',
        ];
    }

    public function map($row): array
    {
        return [
            $row['date'] ? Carbon::parse($row['date'])->format('Y-m-d H:i') : '',
            $row['document_no'] ?? '',
            $row['description'] ?? '',
            $row['receipts_qty'] ?? 0,
            $row['issues_qty'] ?? 0,
            $row['balance'] ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set RTL direction for Arabic text
        $sheet->setRightToLeft(true);
        
        // Style the header row
        $sheet->getStyle('A1:F1')->applyFromArray([
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
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders to all data cells
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:F{$lastRow}")->applyFromArray([
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

        // Add customer info at the top
        $sheet->insertNewRowBefore(1, 3);
        $sheet->setCellValue('A1', 'تقرير العميل: ' . $this->customer->name);
        $sheet->setCellValue('A2', 'الفترة: من ' . $this->dateFrom . ' إلى ' . $this->dateTo);
        $sheet->setCellValue('A3', 'تاريخ التقرير: ' . now()->format('Y-m-d H:i'));

        // Style the info rows
        $sheet->getStyle('A1:F3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        // Merge cells for info rows
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');

        return $sheet;
    }

    public function title(): string
    {
        return 'تقرير العميل - ' . $this->customer->name;
    }
}