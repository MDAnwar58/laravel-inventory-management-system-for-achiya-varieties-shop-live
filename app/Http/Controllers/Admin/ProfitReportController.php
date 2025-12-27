<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class ProfitReportController extends Controller
{
    public function profit_reports(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $total_earnings = $this->get_total_earn($start_date, $end_date);
        $gross_profit = $this->get_gross_profit($start_date, $end_date);
        $total_sales_products_count = $this->get_total_sales_products_count($start_date, $end_date);
        $total_sales_products_weight_count = $this->get_total_sales_products_weight_count($start_date, $end_date);
        $total_sales_products_foot_count = $this->get_total_sales_products_foot_count($start_date, $end_date);
        $total_sales_products_yard_count = $this->get_total_sales_products_yard_count($start_date, $end_date);
        $total_sales_products_meter_count = $this->get_total_sales_products_meter_count($start_date, $end_date);
        $daily_profits = $this->get_daily_profits($start_date, $end_date);
        $profit_by_products = $this->get_profit_by_products($start_date, $end_date);
        return [
            "start_date" => $start_date,
            "end_date" => $end_date,
            "total_earnings" => $total_earnings,
            "gross_profit" => $gross_profit,
            "total_sales_products_count" => $total_sales_products_count,
            "total_sales_products_weight_count" => $total_sales_products_weight_count,
            "total_sales_products_foot_count" => $total_sales_products_foot_count,
            "total_sales_products_yard_count" => $total_sales_products_yard_count,
            "total_sales_products_meter_count" => $total_sales_products_meter_count,
            "daily_profits" => $daily_profits,
            "profit_by_products" => $profit_by_products,
        ];
    }
    public function get_total_earn($start_date, $end_date)
    {
        return SalesOrder::whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cencel')
            ->whereBetween('order_date', [$start_date, $end_date])
            ->sum('total');
    }
    public function get_gross_profit($start_date, $end_date)
    {
        $sales_orders = SalesOrder::whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->whereBetween('order_date', [$start_date, $end_date])
            ->with('sales_order_products.product')
            ->get();

        $gross_profit = 0;
        $order_total_price = 0;
        $total_cost_price = 0;
        if (count($sales_orders) > 0) {
            foreach ($sales_orders as $key => $order) {
                $order_total_price += $order->total;
                if ($order->sales_order_products->count() > 0) {
                    foreach ($order->sales_order_products as $key => $order_product) {
                        $total_cost_price += $order_product->product->cost_price * $order_product->qty;
                    }
                }
            }
        }
        $gross_profit = $order_total_price - $total_cost_price;
        return $gross_profit;
    }
    public function get_total_sales_products_count($start_date, $end_date)
    {
        $sales_products_count = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($start_date, $end_date) {
            $query->whereNot('status', 'cancelled');
            $query->whereNot('payment_status', 'cancel');
            $query->where('stock_w_type', 'none');
            $query->whereBetween('created_at', [$start_date, $end_date]);
        })->sum('qty');

        return (int) $sales_products_count . ' pcs';
    }
    public function get_total_sales_products_weight_count($start_date, $end_date)
    {
        $sales_products_weight_count = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($start_date, $end_date) {
            $query->whereNot('status', 'cancelled');
            $query->whereNot('payment_status', 'cancel');
            $query->where('stock_w_type', 'kg');
            $query->whereBetween('created_at', [$start_date, $end_date]);
        })->sum('qty');

        return $this->formatNumber($sales_products_weight_count) . $this->kg_or_gm($sales_products_weight_count);
    }
    public function get_total_sales_products_foot_count($start_date, $end_date)
    {
        $sales_products = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($start_date, $end_date) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cencel')
                ->where('stock_w_type', 'ft')
                ->whereBetween('order_date', [$start_date, $end_date]);
        })->get();

        $total_foots = [];
        foreach ($sales_products as $p) {
            $total_foots[] = number_format((float) $p->qty, 2);   // 👈 pushing value to array
        }
        $total_foot = $this->array_sum_any_to_inchi_calculate($total_foots);
        return $this->formatNumberWithOnlyTwoDecimal($total_foot) . $this->ft_or_inchi($total_foot); // $sales_products;
    }
    public function get_total_sales_products_yard_count($start_date, $end_date)
    {
        $sales_products = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($start_date, $end_date) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cencel')
                ->where('stock_w_type', 'yard')
                ->whereBetween('order_date', [$start_date, $end_date]);
        })->get();

        $total_yards = [];
        foreach ($sales_products as $p) {
            $total_yards[] = number_format((float) $p->qty, 2);   // 👈 pushing value to array
        }
        $total_yard = $this->array_sum_any_to_inchi_calculate($total_yards, 36);
        return $this->formatNumberWithOnlyTwoDecimal($total_yard) . $this->yard_or_inchi($total_yard); // $sales_products;
    }
    public function get_total_sales_products_meter_count($start_date, $end_date)
    {
        $sales_products = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($start_date, $end_date) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cencel')
                ->where('stock_w_type', 'm')
                ->whereBetween('order_date', [$start_date, $end_date]);
        })->get();

        $total_meters = [];
        foreach ($sales_products as $p) {
            $total_meters[] = number_format((float) $p->qty, 2);   // 👈 pushing value to array
        }
        $total_meter = $this->array_sum_any_to_inchi_calculate($total_meters, 39);
        return $this->formatNumberWithOnlyTwoDecimal($total_meter) . $this->m_or_inchi($total_meter); // $sales_products;
    }
    public function get_daily_profits($start_date, $end_date)
    {
        $sales_orders = SalesOrder::whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->whereBetween('order_date', [$start_date, $end_date])
            ->with('sales_order_products.product')
            ->get();

        // Initialize daily profits array with ALL dates
        $daily_profits = [];

        $period = CarbonPeriod::create($start_date, $end_date); // ✅ includes both dates
        foreach ($period as $date) {
            $daily_profits[$date->toDateString()] = [
                'date' => $date->toDateString(),
                'revenue' => 0,
                'cost' => 0,
                'profit' => 0,
            ];
        }

        // Fill in sales data
        foreach ($sales_orders as $order) {
            $date = Carbon::parse($order->order_date)->toDateString();

            // Add order revenue
            $daily_profits[$date]['revenue'] += $order->total;

            // Add order cost
            foreach ($order->sales_order_products as $order_product) {
                $daily_profits[$date]['cost'] += $order_product->product->cost_price * $order_product->qty;
            }
        }

        // Finalize profit per day
        foreach ($daily_profits as &$day) {
            $day['profit'] = round(($day['revenue'] - $day['cost']), 2);
            $day['revenue'] = round($day['revenue'], 2);
            $day['cost'] = round($day['cost'], 2);
        }

        // Reset keys for clean JSON / Blade use
        return array_values($daily_profits);
    }
    public function get_profit_by_products($start_date, $end_date)
    {
        $products = Product::whereHas('sales_orders_products.salesOrder', function ($query) use ($start_date, $end_date) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$start_date, $end_date]);
        })
            ->with([
                'sales_orders_products' => function ($q) use ($start_date, $end_date) {
                    $q->whereHas('salesOrder', function ($subQuery) use ($start_date, $end_date) {
                        $subQuery->whereNot('status', 'cancelled')
                            ->whereNot('payment_status', 'cancel')
                            ->whereBetween('order_date', [$start_date, $end_date]);
                    });
                },
                'sales_orders_products.salesOrder'
            ])
            ->get();

        $profited_products = [];
        foreach ($products as $product) {
            $qtyValues = array_map('floatval', array_column($product->sales_orders_products->toArray(), 'qty'));
            $p = $product->sales_orders_products->firstWhere('product_id', $product->id);
            $stock_w_type = $p->stock_w_type;

            $profited_products[] = [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $product->image,
                'total_units' => !in_array('none', array_column($product->sales_orders_products->toArray(), 'stock_w_type'))
                    ? $this->formatNumber(array_sum($qtyValues), $stock_w_type) . $this->any_or_gm_or_inch(array_sum($qtyValues), $stock_w_type)
                    : array_sum($qtyValues) . ' pcs',
                'total_sales' => array_sum(array_column($product->sales_orders_products->toArray(), 'total')),
                'total_profit' => array_sum(array_column($product->sales_orders_products->toArray(), 'total')) - ($product->cost_price * array_sum(array_column($product->sales_orders_products->toArray(), 'qty'))),
            ];
        }

        return $profited_products;
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
            // strip trailing .000 etc. after 3-decimal rounding
            return rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
        }
    }
    public function formatNumberWithOnlyTwoDecimal($num)
    {
        $n = (float) $num;
        if ($n < 1)
            $repl = (string) intval(round($n * 1000 / 10));
        else
            $repl = rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        return $repl;
    }
    public function any_or_gm_or_inch($num, $type = '')
    {
        if ($type === 'kg') {
            $n = (float) $num;
            return $n < 1 ? $n > 0 && $n < 1 ? 'gm' : '' : 'kg';
        } elseif ($type === 'ft') {
            $n = (float) $num;
            return $n < 1 ? $n > 0 && $n < 1 ? 'gm' : '' : 'ft';
        } elseif ($type === 'yard') {
            $n = (float) $num;
            return $n < 1 ? $n > 0 && $n < 1 ? 'gm' : '' : 'yard';
        } elseif ($type === 'm') {
            $n = (float) $num;
            return $n < 1 ? $n > 0 && $n < 1 ? 'gm' : '' : 'm';
        } else {
            return '';
        }
    }
    public function kg_or_gm($num)
    {
        $n = (float) $num;
        return $n < 1 ? $n > 0 && $n < 1 ? 'gm' : '' : 'kg';
    }
    public function ft_or_inchi($num)
    {
        $n = (float) $num;
        return $n < 1 ? $n > 0 && $n < 1 ? 'inchi' : '' : 'ft';
    }
    public function yard_or_inchi($num)
    {
        $n = (float) $num;
        return $n < 1 ? $n > 0 && $n < 1 ? 'inchi' : '' : 'yard';
    }
    public function m_or_inchi($num)
    {
        $n = (float) $num;
        return $n < 1 ? $n > 0 && $n < 1 ? 'inchi' : '' : 'm';
    }
    private function array_sum_any_to_inchi_calculate(array $values, $fixed_inchi = 12)
    {
        $totalFeet = 0;
        $totalInches = 0;

        foreach ($values as $value) {
            $value = (float) $value;
            $feet = floor($value);
            $inches = round(($value - $feet) * 100);

            $totalFeet += $feet;
            $totalInches += $inches;
        }

        // Convert extra inches to feet
        $extraFeet = floor($totalInches / $fixed_inchi);
        $remainingInches = $totalInches % $fixed_inchi;

        $totalFeet += $extraFeet;

        return $totalFeet + ($remainingInches / 100);
    }
}
