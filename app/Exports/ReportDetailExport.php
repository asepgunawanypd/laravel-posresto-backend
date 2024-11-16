<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportDetailExport implements FromArray, WithHeadings, WithStyles
{
    protected $details;
    protected $totalAmount;

    public function __construct($details, $totalAmount)
    {
        $this->details = $details;
        // $this->totalAmount = $totalAmount;
        $this->totalAmount = $details->sum('total');
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->details as $order) {
            $orderTotal = $order->orderDetails->sum(function ($detail) {
                return $detail->quantity * $detail->product->price;
            });

            // Main Order Row
            $rows[] = [
                'Order ID' => $order->id,
                'Transaction Date' => $order->created_at->format('Y-m-d'),
                'Discount' => $order->discount,
                'Cashier' => $order->nama_kasir,
                'Total Amount' => number_format($order->total, 0),
            ];

            // Sub-rows for Order Details
            $rows[] = [
                'Order ID' => 'Product Details:',
                'Transaction Date' => '',
                'Cashier' => '',
                'Total Amount' => '',
            ];
            $rows[] = [
                'Order ID' => 'Product Name',
                'Transaction Date' => 'Quantity',
                'Cashier' => 'Price',
                'Total Amount' => 'Subtotal',
            ];

            foreach ($order->orderDetails as $detail) {
                $rows[] = [
                    'Order ID' => $detail->product->name,
                    'Transaction Date' => $detail->quantity,
                    'Cashier' => number_format($detail->product->price, 0),
                    'Total Amount' => number_format($detail->quantity * $detail->product->price, 0),
                ];
            }

            // Spacer Row
            $rows[] = [];
        }

        // Add Grand Total
        $rows[] = [
            'Order ID' => '',
            'Transaction Date' => '',
            'Discount' => '',
            'Cashier' => 'Grand Total:',
            'Total Amount' => number_format($this->totalAmount, 0),
        ];

        return $rows;
    }

    public function headings(): array
    {
        return ['Order ID', 'Transaction Date', 'Discount', 'Cashier', 'Total Amount'];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        return $sheet;
    }
}
