<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $sales;
    protected $totalAmount;

    public function __construct($sales, $totalAmount)
    {
        $this->sales = $sales;
        $this->totalAmount = $totalAmount;
    }

    public function collection()
    {
        // Map the sales data
        $salesData = $this->sales->map(function ($sale) {
            return [
                'ID' => $sale->id,
                'Date' => $sale->created_at->format('Y-m-d'),
                'Total Items' => $sale->total_item,
                'Tax' => $sale->tax,
                'Discount' => $sale->discount,
                'Total' => number_format($sale->total),
                'Cashier' => $sale->nama_kasir,
            ];
        });

        // Add the total row
        $salesData->push([
            'ID' => '',
            'Date' => '',
            'Total Items' => '',
            'Tax' => '',
            'Discount' => 'Total Amount',
            'Total' => number_format($this->totalAmount, 0),
            'Cashier' => '',
        ]);

        return $salesData;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Total Items',
            'Tax',
            'Discount',
            'Total',
            'Cashier',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply borders to all cells containing data
        $lastRow = $this->sales->count() + 2; // Account for heading row and total row
        $sheet->getStyle('A1:G' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // Bold headings
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        // Align totals row text
        $sheet->getStyle('A' . $lastRow . ':G' . $lastRow)->getFont()->setBold(true);
        $sheet->getStyle('F' . $lastRow)->getAlignment()->setHorizontal('center');

        return $sheet;
    }
}
