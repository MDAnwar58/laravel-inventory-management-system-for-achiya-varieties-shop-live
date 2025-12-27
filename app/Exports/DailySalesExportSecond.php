<?php

namespace App\Exports;

use App\Models\SalesOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailySalesExportSecond implements FromCollection, WithMapping, WithHeadings, WithStyles
{
    protected $start_date;
    protected $end_date;

    protected $previousDate = null;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        $orders = SalesOrder::with('sales_order_products.product', 'customer')
            ->whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->whereBetween('order_date', [$this->start_date, $this->end_date])
            ->orderBy('order_date')
            ->get();

        $period = \Carbon\CarbonPeriod::create($this->start_date, $this->end_date);

        $data = collect();

        foreach ($period as $date) {
            $dateOrders = $orders->filter(function ($order) use ($date) {
                return \Carbon\Carbon::parse($order->order_date)->isSameDay($date);
            });

            if ($dateOrders->isEmpty()) {
                $data->push((object) [
                    'order_date' => $date->format('Y-m-d'),
                    'no_order' => true,
                    'sales_order_products' => []
                ]);
            } else {
                foreach ($dateOrders as $order) {
                    $data->push($order);
                }
            }
        }

        return $data;
    }

    public function map($order): array
    {
        $rows = [];

        if (isset($order->no_order)) {
            $rows[] = [
                date('d M, Y', strtotime($order->order_date)),
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A'
            ];
            return $rows;
        }

        $currentDate = date('d M, Y', strtotime($order->order_date));

        if ($this->previousDate !== $currentDate) {
            $rows[] = [
                $currentDate,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ];
            $this->previousDate = $currentDate;
        }

        $firstProduct = true;
        foreach ($order->sales_order_products as $item) {
            $rows[] = [
                '', // hide date repetition
                $firstProduct ? $order->order_number : '',
                $firstProduct ? ($order->customer->name ?? '') : '',
                $firstProduct ? ucfirst($order->status) : '',
                $firstProduct ? ucfirst($order->payment_status) : '',
                $item->product->name ?? '',
                $item->qty ?? '',
                $item->price ?? '',
                $item->discount_price ?? '',
            ];
            $firstProduct = false;
        }

        return $rows;
    }



    public function headings(): array
    {
        return [
            'Order Date',
            'Order Number',
            'Customer',
            'Status',
            'Payment Status',
            'Product Name',
            'Qty',
            'Price',
            'Discount Price',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1F4E78'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:I' . $highestRow)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $columns = [
            'A' => 18,
            'B' => 30,
            'C' => 25,
            'D' => 15,
            'E' => 20,
            'F' => 30,
            'G' => 10,
            'H' => 15,
            'I' => 15,
        ];

        foreach ($columns as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }
}
