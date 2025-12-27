<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    private $total_cost_price = 0;

    public function collection()
    {
        return Product::with(['item_type', 'brand', 'category', 'sub_category'])
            ->get()
            ->map(function ($product) {
                // accumulate totals
                // $this->total_price += $product->price;
                $sub_total_cost_price = 0;

                if ($product->stock_w_type === 'none') {
                    $sub_total_cost_price = $product->cost_price * $product->stock;
                } elseif ($product->stock_w_type === 'kg') {
                    $sub_total_cost_price = $product->cost_price * $product->stock_w;
                } elseif ($product->stock_w_type === 'ft') {
                    $sub_total_cost_price = $this->calculatePrice($product->cost_price, $product->stock_w);
                } elseif ($product->stock_w_type === 'yard') {
                    $sub_total_cost_price = $this->calculatePrice($product->cost_price, $product->stock_w, 36);
                } elseif ($product->stock_w_type === 'm') {
                    $sub_total_cost_price = $this->calculatePrice($product->cost_price, $product->stock_w, 39);
                }
                $this->total_cost_price += $sub_total_cost_price;


                return [
                    'Product Name' => $product->name,
                    'Item Type' => $product->item_type?->name ?? '...',
                    'Brand' => $product->brand?->name ?? '...',
                    'Category' => $product->category?->name ?? '...',
                    'Sub Category' => $product->sub_category?->name ?? '...',
                    'Stock' => $product->stock_w_type === 'none' ? ((int) $product->stock) . ' pcs' : $this->formatNumber($product->stock_w, $product->stock_w_type) . $this->kg_or_gm_or_ft_or_yard_or_m_inch($product->stock_w, $product->stock_w_type),
                    'Wholesale Price' => '৳' . number_format($product->price, 2),
                    'Retail Price' => $product->retail_price ? '৳' . number_format($product->retail_price, 2) : '...',
                    'Cost Price' => '৳' . number_format($product->cost_price, 2),
                    'Total Cost Price' => '৳' . number_format($sub_total_cost_price, 2),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Item Type',
            'Brand',
            'Category',
            'Sub Category',
            'Stock',
            'Wholesale Price',
            'Retail Price',
            'Cost Price',
            'Total Cost Price',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header style
        $sheet->getStyle('A1:J1')->applyFromArray([
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
        $sheet->getStyle('A2:J' . $highestRow)
            ->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);


        // Column widths
        $columns = [
            'A' => 35,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
        ];

        foreach ($columns as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow() + 1;

                // Add totals row
                $sheet->setCellValue('A' . $lastRow, 'TOTAL');
                // $sheet->setCellValue('G' . $lastRow, '৳' . number_format($this->total_price, 2));
                $sheet->setCellValue('J' . $lastRow, '৳' . number_format($this->total_cost_price, 2));

                // Style totals row
                $sheet->getStyle('A' . $lastRow . ':J' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFD3D3D3'],
                    ],
                ])
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            }
        ];
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

    private function kg_or_gm_or_ft_or_yard_or_m_inch($num, $type = '')
    {
        $n = (float) $num;
        if ($type === 'kg') {
            return $n < 1 ? 'gm' : 'kg';
        } elseif ($type === 'ft') {
            return $n < 1 ? 'inchi' : 'ft';
        } elseif ($type === 'yard') {
            return $n < 1 ? 'inchi' : 'yard';
        } else {
            return $n < 1 ? 'inchi' : 'm';
        }
    }
    public function calculatePrice($price = 0, $stock_float = 0, $inch_limit = 12)
    {
        $foot = 0;
        $inches = 0;
        $totalPrice = 0;

        // If array format [foot, yard, meter and inches]
        if (isset($stock_float)) {
            $foot = (int) $stock_float;
            $strInch = preg_replace('/^[^\.]*\./', '', (string) $stock_float);
            $inches = (int) $strInch;
        }

        $totalFeet = $foot + ($inches / $inch_limit);
        $totalPrice = $price * $totalFeet;

        return $totalPrice;
    }
}
