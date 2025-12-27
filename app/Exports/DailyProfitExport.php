<?php

namespace App\Exports;

use App\Models\SalesOrder;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailyProfitExport implements FromCollection, WithHeadings, WithStyles
{
    protected $start_date;
    protected $end_date;

    private $total_sales = 0;
    private $total_cost = 0;
    private $total_profit = 0;

    public function __construct($start_date, $end_date)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function collection()
    {
        $start = Carbon::parse($this->start_date)->startOfDay()->setTimezone(config('app.timezone'));
        $end = Carbon::parse($this->end_date)->endOfDay()->setTimezone(config('app.timezone'));

        // Fetch all orders within range
        $orders = SalesOrder::whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->whereBetween('order_date', [$start, $end])
            ->with('sales_order_products.product')
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
            ]);

            if (isset($orders[$date])) {
                // Group product-level sales for this date
                $products = [];

                foreach ($orders[$date] as $order) {
                    foreach ($order->sales_order_products as $item) {
                        $productId = $item->product_id ?? 'N/A';
                        $productName = $item->product?->name ?? 'N/A';
                        $salesProductPrice = $item->price ?? 'N/A';
                        $qty = (float) $item->qty;
                        $sales = (float) $item->total;
                        $cost = (float) ($item->product?->cost_price ?? 0) * $qty;

                        if (!isset($products[$productId])) {
                            $products[$productId] = [
                                'price' => $salesProductPrice,
                                'retail_price_status' => $item->retail_price_status,
                                'stock_w_type' => $item->stock_w_type,
                                'qty' => 0,
                                'sales' => 0,
                                'cost' => 0,
                            ];
                        }

                        $products[$productId]['qty'] += $qty;
                        $products[$productId]['sales'] += $sales;
                        $products[$productId]['cost'] += $cost;
                    }
                }

                foreach ($products as $productId => $info) {
                    $profit = $info['sales'] - $info['cost'];

                    $rows->push([
                        '', // empty under date
                        $productName . ' (' . $productId . ')',
                        $info['retail_price_status'] === 0 ? '৳' . number_format($info['price'], 2) : '...',
                        $info['retail_price_status'] !== 0 ? '৳' . number_format($info['price'], 2) : '...',
                        $info['stock_w_type'] === 'none' ? ((int) $info['qty']) . ' pcs' : $this->formatNumber($info['qty']) . $this->kg_or_gm($info['qty']),
                        '৳' . number_format($info['sales'], 2),
                        '৳' . number_format($info['cost'], 2),
                        '৳' . number_format($profit, 2),
                    ]);

                    $this->total_sales += $info['sales'];
                    $this->total_cost += $info['cost'];
                    $this->total_profit += $profit;
                }
            } else {
                // No sales on this date
                $rows->push([
                    '',
                    'No Sales',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—',
                    '—'
                ]);
            }
        }

        // Final summary row
        $rows->push([
            'Total Summary',
            '———',
            '———',
            '———',
            '———',
            '৳' . number_format($this->total_sales, 2),
            '৳' . number_format($this->total_cost, 2),
            '৳' . number_format($this->total_profit, 2),
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Product Name',
            'Price',
            'Retail Price',
            'Total Quantity Sold',
            'Total Sales Amount',
            'Total Cost Amount',
            'Total Profit',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:H1')->applyFromArray([
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

        // Center align all other cells
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:H' . $highestRow)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        // Set column widths
        $columns = [
            'A' => 18,
            'B' => 30,
            'C' => 20,
            'D' => 20,
            'E' => 22,
            'F' => 22,
            'G' => 22,
            'H' => 22,
        ];

        foreach ($columns as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }
    private function formatNumber($num)
    {
        $n = (float) $num;
        if ($n < 1)
            return preg_replace('/\.0+$/', '', (string) ($n * 1000));
        return rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
    }

    private function kg_or_gm($num)
    {
        $n = (float) $num;
        return $n < 1 ? 'gm' : 'kg';
    }
}
