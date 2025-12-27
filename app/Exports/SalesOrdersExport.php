<?php

namespace App\Exports;

use App\Models\SalesOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class SalesOrdersExport implements FromCollection, WithHeadings, WithStyles
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection()
    {
        $order = SalesOrder::with('customer', 'sales_order_products.product')
            ->findOrFail($this->id);

        // 1️⃣ Map products without Discount Price
        $products = $order->sales_order_products->map(function ($orderProduct, $i) use ($order) {
            return [
                'Order Number' => $i + 1 === 1 ? $order->order_number : '',
                'Customer' => $i + 1 === 1 ? $order->customer?->name : '',
                'Order Date' => $i + 1 === 1 ? date('d M, Y', strtotime($order->order_date)) : '',
                'Due Date' => $i + 1 === 1 ? (!empty($order->due_date) ? date('d M, Y', strtotime($order->due_date)) : '...') : '',
                'Status' => $i + 1 === 1 ? $order->status : '',
                'Payment Status' => $i + 1 === 1 ? $order->payment_status : '',
                'Cancelled Date' => $i + 1 === 1 ? $order->status === 'cancelled' && $order->cancelled_date ? date('d M, Y', strtotime($order->cancelled_date)) : '...' : '',
                'Product' => $orderProduct->product?->name,
                'Price' => '৳' . number_format($orderProduct->price, 2),
                'Quantity/Weight' => ($orderProduct->stock_w_type !== 'none'
                    ? $this->formatNumber($orderProduct->qty, $orderProduct->stock_w_type) . '' . $this->kg_or_gm_or_ft($orderProduct->qty, $orderProduct->stock_w_type)
                    : (int) $orderProduct->qty . ' pcs'),
                'Total Price' => '৳' . number_format($orderProduct->total, 2),
                'Sales Status' => ((bool) $orderProduct->retail_price_status) === false
                    ? 'Wholesale'
                    : 'Retail Sales',
            ];
        });

        // 2️⃣ Summary Rows
        $summaryRow = collect([
            [
                'Order Number' => '',
                'Customer' => '',
                'Order Date' => '',
                'Due Date' => '',
                'Status' => '',
                'Payment Status' => '',
                'Cancelled Date' => '',
                'Product' => '',
                'Price' => '',
                'Quantity/Weight' => '',
                'Total Price' => "Total Amount: " . '৳' . number_format($order->total, 2),
                'Sales Status' => '',
            ],
            [
                'Order Number' => '',
                'Customer' => '',
                'Order Date' => '',
                'Due Date' => '',
                'Status' => '',
                'Payment Status' => '',
                'Cancelled Date' => '',
                'Product' => '',
                'Price' => '',
                'Quantity/Weight' => '',
                'Total Price' => "Paid Amount: " . '৳' . number_format($order->paid_amount ?? 0, 2),
                'Sales Status' => '',
            ],
            [
                'Order Number' => '',
                'Customer' => '',
                'Order Date' => '',
                'Due Date' => '',
                'Status' => '',
                'Payment Status' => '',
                'Cancelled Date' => '',
                'Product' => '',
                'Price' => '',
                'Quantity/Weight' => '',
                'Total Price' => "Due Amount: " . '৳' . number_format($order->due_amount ?? 0, 2),
                'Sales Status' => '',
            ]
        ]);

        return $products->concat($summaryRow);
    }

    // 🏷️ 3️⃣ Headings (Discount Price Removed)
    public function headings(): array
    {
        return [
            'Order Number',
            'Customer',
            'Order Date',
            'Due Date',
            'Status',
            'Payment Status',
            'Cancelled Date',
            'Product',
            'Price',
            'Quantity/Weight',
            'Total Price',
            'Sales Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header Style
        $sheet->getStyle('A1:L1')->applyFromArray([
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

        // Center all other rows
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:L' . $highestRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:L' . $highestRow)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Column widths (Adjusted)
        $columns = [
            'A' => 30,
            'B' => 35,
            'C' => 18,
            'D' => 18,
            'E' => 15,
            'F' => 20,
            'G' => 20,
            'H' => 35,
            'I' => 15,
            'J' => 20,
            'K' => 25,
            'L' => 20,
        ];

        foreach ($columns as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }

    public function formatNumber($num, $type = '')
    {
        $n = (float) $num;
        if ($type === 'ft' || $type === 'yard' || $type === 'm') {
            if ($n < 1)
                $repl = (string) intval(round($n * 1000 / 10));
            else
                $repl = rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
            return $repl;
        } else {
            if ($n < 1) {
                return preg_replace('/\.0+$/', '', (string) ($n * 1000));
            }
            return rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
        }
    }

    public function kg_or_gm_or_ft($num, $type = 'kg')
    {
        if ($type === 'kg') {
            $n = (float) $num;
            return $n < 1 ? 'gm' : 'kg';
        } elseif ($type === 'ft') {
            $n = (float) $num;
            return $n < 1 ? 'inchi' : 'ft';
        } elseif ($type === 'yard') {
            $n = (float) $num;
            return $n < 1 ? 'inchi' : 'yard';
        } else {
            $n = (float) $num;
            return $n < 1 ? 'inchi' : 'm';
        }
    }
}
