<?php

namespace App\Exports;

use App\Models\SalesOrder;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailySalesExport implements FromCollection, WithHeadings, WithStyles
{
    private $total_due_amount = 0;
    private $total_paid_amount = 0;
    private $total_amount = 0;
    protected $start_date;
    protected $end_date;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        $start = Carbon::parse($this->start_date)->startOfDay()->setTimezone(config('app.timezone'));
        $end = Carbon::parse($this->end_date)->endOfDay()->setTimezone(config('app.timezone'));

        $orders = SalesOrder::with('customer', 'sales_order_products.product')
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', '!=', 'cancel')
            ->whereBetween('order_date', [$start, $end])
            ->oldest()
            ->get()
            ->groupBy(
                fn($order) => Carbon::parse($order->order_date)
                    ->setTimezone(config('app.timezone'))  // e.g., 'Asia/Dhaka'
                    ->format('d M, Y')
            );

        $rows = collect();

        // Generate continuous date range
        $start = new \DateTime($this->start_date);
        $end = new \DateTime($this->end_date);
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($start, $interval, $end->modify('+1 day'));

        foreach ($period as $dateObj) {
            $date = $dateObj->format('d M, Y');

            // Date Header Row
            $rows->push([
                $date,
                '—',
                '—',
                '—',
                '—',
                '—',
                '—',
                '—',
                '—',
                '—',
                '—',
                '—',
                '—',
                '—',
                '—',
                '—',
                '—',
            ]);

            if (isset($orders[$date])) {
                foreach ($orders[$date] as $order) {
                    $firstProduct = true;

                    if ($order->sales_order_products->count() > 0) {
                        foreach ($order->sales_order_products as $item) {
                            $rows->push([
                                '', // Empty because we already have date header
                                $firstProduct ? $order->order_number : '',
                                $firstProduct ? ($order->customer?->name ?? '') : '',
                                $firstProduct ? ($order->customer?->phone ?? '...') : '',
                                $firstProduct ? ($order->customer?->address ?? '...') : '',
                                $firstProduct ? ucfirst($order->status) : '',
                                $firstProduct ? ucfirst($order->payment_status) : '',
                                $firstProduct ? $this->formatDate($order->due_date, !empty($order->order_date)) : '',
                                $firstProduct ? $this->formatDate($order->cancelled_date, !empty($order->order_date)) : '',
                                $item->product?->name ?? '',
                                $this->formatPrice($item, false),
                                $this->formatPrice($item, true),
                                $item->stock_w_type !== 'none'
                                ? (($this->formatNumber($item->qty, $item->stock_w_type) . $this->kg_or_gm_ft_or_yard_m_ot_inch($item->qty, $item->stock_w_type)))
                                : (isset($item->qty) ? ((int) $item->qty . ' pcs') : ''),
                                !empty($item->total) ? '৳' . number_format((float) $item->total, 2) : 'N/A',
                                $firstProduct ? (!empty($order->total) ? '৳' . number_format((float) $order->total, 2) : 'N/A') : '',
                                $firstProduct ? (!empty($order->paid_amount) ? '৳' . number_format((float) $order->paid_amount, 2) : 'N/A') : '',
                                $firstProduct ? (!empty($order->due_amount)
                                    ? ($order->due_amount > 0
                                        ? '৳' . number_format((float) $order->due_amount, 2)
                                        : '...')
                                    : 'N/A')
                                : '...',
                                $firstProduct ? $item->retail_price_status !== 0 ? 'Retail Sales' : 'Wholesale Sales' : '',
                            ]);

                            $this->total_due_amount += $firstProduct ? (float) $order->due_amount : 0;
                            $this->total_paid_amount += $firstProduct ? (float) $order->paid_amount : 0;
                            $this->total_amount += $firstProduct ? (float) $order->total : 0;

                            $firstProduct = false;
                        }
                    } else {
                        // No products for this order
                        $rows->push([
                            '',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                        ]);
                    }
                }
            } else {
                // No orders for this date
                $rows->push([
                    '',
                    'No Orders',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                ]);
            }
        }

        // Final summary
        $rows->push([
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Total Amount=' . number_format((float) $this->total_amount, 2),
            'Total Paid Amount=' . number_format((float) $this->total_paid_amount, 2),
            'Total Due Amount=' . number_format((float) $this->total_due_amount, 2),
            '',
        ]);

        return $rows;
    }


    public function headings(): array
    {
        return [
            'Order Date',
            'Order Id',
            'Customer',
            'Customer Phone',
            'Customer Address',
            'Status',
            'Payment Status',
            'Payment Due Date',
            'Payment Cancelled Date',
            'Product Name',
            'Wholesale Price',
            'Retail Price',
            'Quantity/Weights',
            'Sub Total Amount',
            'Total Amount',
            'Paid Amount',
            'Due Amount',
            'Sales Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
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
        $sheet->getStyle('A2:R' . $highestRow)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        foreach ([
            'A' => 18,
            'B' => 30,
            'C' => 25,
            'D' => 23,
            'E' => 35,
            'F' => 15,
            'G' => 20,
            'H' => 20,
            'I' => 23,
            'J' => 30,
            'K' => 15,
            'L' => 15,
            'M' => 23,
            'N' => 15,
            'O' => 33,
            'P' => 33,
            'Q' => 33,
            'R' => 33,
        ] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }

    private function formatDate($date, $isNotSales = false)
    {
        if (empty($date) && $isNotSales)
            return '...';
        if (empty($date) || $date === 'N/A' || $date === '...')
            return 'N/A';
        return date('d M, Y', strtotime($date));
    }

    private function formatNumber($num, $type = '')
    {
        $n = (float) $num;
        if ($type === 'ft' || $type === 'yard' || $type === 'm') {
            if ($n < 1)
                $repl = (string) intval(round($n * 1000 / 10));
            else
                $repl = rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
            return $repl;
        } else {
            if ($n < 1)
                return preg_replace('/\.0+$/', '', (string) ($n * 1000));
            return rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
        }
    }

    private function kg_or_gm_ft_or_yard_m_ot_inch($num, $type = '')
    {
        $n = (float) $num;
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

    private function formatPrice($item, $isRetail = false)
    {
        if (!isset($item->price) || $item->price === 'N/A' || $item->price === null)
            return 'N/A';
        if (!isset($item->retail_price_status))
            return 'N/A';

        if ($isRetail) {
            return $item->retail_price_status !== 0
                ? '৳' . number_format((float) $item->price, 2)
                : '...';
        } else {
            return $item->retail_price_status === 0
                ? '৳' . number_format((float) $item->price, 2)
                : '...';
        }
    }
}
