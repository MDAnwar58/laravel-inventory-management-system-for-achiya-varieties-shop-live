<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{

    public function sales_reports(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $total_sales_orders_count = $this->get_total_sales_orders_count($start_date, $end_date);
        $total_sales_products_units = $this->get_total_sales_products_units($start_date, $end_date);
        $total_sales_products_weights = $this->get_total_sales_products_weights($start_date, $end_date);
        $total_sales_products_foots = $this->get_total_sales_products_foots($start_date, $end_date);
        $total_sales_products_yards = $this->get_total_sales_products_yards($start_date, $end_date);
        $total_sales_products_meters = $this->get_total_sales_products_meters($start_date, $end_date);
        $total_sales_earnings = $this->get_total_sales_earnings($start_date, $end_date);
        $daily_sales = $this->get_daily_sales($start_date, $end_date);
        $daily_paid_and_due_sales = $this->get_daily_paid_and_due_sales($start_date, $end_date);
        $sales_by_products = $this->get_sales_by_products($start_date, $end_date);
        return [
            'date' => [
                'start' => $start_date,
                'end' => $end_date,
            ],
            'total_sales_orders_count' => $total_sales_orders_count,
            'total_sales_products_units' => $total_sales_products_units,
            'total_sales_products_weights' => $total_sales_products_weights,
            'total_sales_products_foots' => $total_sales_products_foots,
            'total_sales_products_yards' => $total_sales_products_yards,
            'total_sales_products_meters' => $total_sales_products_meters,
            'total_sales_earnings' => $total_sales_earnings,
            'daily_sales' => $daily_sales,
            'daily_paid_and_due_sales' => $daily_paid_and_due_sales,
            'sales_by_products' => $sales_by_products,
        ];
    }
    public function get_total_sales_orders_count($start_date, $end_date)
    {
        $sales_orders = SalesOrder::whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cencel')
            ->whereBetween('order_date', [$start_date, $end_date])
            ->get();
        return $sales_orders->count();
    }
    public function get_total_sales_earnings($start_date, $end_date)
    {
        $total_earnings = SalesOrder::whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cencel')
            ->whereBetween('order_date', [$start_date, $end_date])
            ->sum('total');
        return $total_earnings;
    }
    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Get total sales products units count between given dates
     *
     * @param string $start_date start date in format 'Y-m-d'
     * @param string $end_date end date in format 'Y-m-d'
     * @return int total sales products units count
     */
    /*******  b9b27028-a7a9-43d4-a5c0-3bd8f8869f34  *******/
    public function get_total_sales_products_units($start_date, $end_date)
    {
        $sales_products = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($start_date, $end_date) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cencel')
                ->where('stock_w_type', 'none')
                ->whereBetween('order_date', [$start_date, $end_date]);
        })->sum('qty');
        return (int) $sales_products . ' pcs';
    }
    public function get_total_sales_products_weights($start_date, $end_date)
    {
        $sales_products = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($start_date, $end_date) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cencel')
                ->where('stock_w_type', 'kg')
                ->whereBetween('order_date', [$start_date, $end_date]);
        })->sum('qty');
        return $this->formatNumber($sales_products) . $this->kg_or_gm($sales_products); // $sales_products;
    }
    public function get_total_sales_products_foots($start_date, $end_date)
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
    public function get_total_sales_products_yards($start_date, $end_date)
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
    public function get_total_sales_products_meters($start_date, $end_date)
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
    public function get_daily_sales($start_date, $end_date)
    {
        // Step 1: Get sales totals per day (from sales_orders only)
        $salesTotals = DB::table('sales_orders')
            ->selectRaw('DATE(order_date) as date, SUM(total) as total_sales')
            ->whereBetween('order_date', [$start_date, $end_date])
            ->whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->groupBy('date')
            ->pluck('total_sales', 'date'); // [date => total_sales]

        // Step 2: Get product qty per day (from sales_order_products)
        $productTotals = DB::table('sales_orders')
            ->join('sales_order_products', 'sales_orders.id', '=', 'sales_order_products.sales_order_id')
            ->selectRaw('DATE(sales_orders.order_date) as date, SUM(sales_order_products.qty) as total_products_count')
            ->whereBetween('sales_orders.order_date', [$start_date, $end_date])
            ->whereNot('sales_orders.status', 'cancelled')
            ->whereNot('sales_orders.payment_status', 'cancel')
            ->where('sales_order_products.stock_w_type', '=', 'none')
            ->groupBy('date')
            ->pluck('total_products_count', 'date'); // [date => total_products_count]

        // Step 3: Get product qty per day (from sales_order_products)
        $productWeightTotals = DB::table('sales_orders')
            ->join('sales_order_products', 'sales_orders.id', '=', 'sales_order_products.sales_order_id')
            ->selectRaw('DATE(sales_orders.order_date) as date, SUM(sales_order_products.qty) as total_products_count')
            ->whereBetween('sales_orders.order_date', [$start_date, $end_date])
            ->whereNot('sales_orders.status', 'cancelled')
            ->whereNot('sales_orders.payment_status', 'cancel')
            ->where('sales_order_products.stock_w_type', '=', 'kg')
            ->groupBy('date')
            ->pluck('total_products_count', 'date');

        $productFootTotals = DB::table('sales_orders')
            ->join('sales_order_products', 'sales_orders.id', '=', 'sales_order_products.sales_order_id')
            ->selectRaw('DATE(sales_orders.order_date) as date, SUM(sales_order_products.qty) as total_products_count')
            ->whereBetween('sales_orders.order_date', [$start_date, $end_date])
            ->whereNot('sales_orders.status', 'cancelled')
            ->whereNot('sales_orders.payment_status', 'cancel')
            ->where('sales_order_products.stock_w_type', '=', 'ft')
            ->groupBy('date')
            ->pluck('total_products_count', 'date');

        $productYardTotals = DB::table('sales_orders')
            ->join('sales_order_products', 'sales_orders.id', '=', 'sales_order_products.sales_order_id')
            ->selectRaw('DATE(sales_orders.order_date) as date, SUM(sales_order_products.qty) as total_products_count')
            ->whereBetween('sales_orders.order_date', [$start_date, $end_date])
            ->whereNot('sales_orders.status', 'cancelled')
            ->whereNot('sales_orders.payment_status', 'cancel')
            ->where('sales_order_products.stock_w_type', '=', 'yard')
            ->groupBy('date')
            ->pluck('total_products_count', 'date');

        $productMeterTotals = DB::table('sales_orders')
            ->join('sales_order_products', 'sales_orders.id', '=', 'sales_order_products.sales_order_id')
            ->selectRaw('DATE(sales_orders.order_date) as date, SUM(sales_order_products.qty) as total_products_count')
            ->whereBetween('sales_orders.order_date', [$start_date, $end_date])
            ->whereNot('sales_orders.status', 'cancelled')
            ->whereNot('sales_orders.payment_status', 'cancel')
            ->where('sales_order_products.stock_w_type', '=', 'm')
            ->groupBy('date')
            ->pluck('total_products_count', 'date');

        // Step 3: Generate full date range
        $period = new \DatePeriod(
            new Carbon($start_date),
            new \DateInterval('P1D'),
            (new Carbon($end_date))->addDay()
        );

        // Step 4: Merge results
        $report = collect();
        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            $report->push([
                'date' => $d,
                'total_sales' => (float) ($salesTotals[$d] ?? 0),
                'total_products_count' => (int) ($productTotals[$d] ?? 0),
                'total_products_weights' => isset($productWeightTotals[$d]) && $productWeightTotals[$d] > 0 ? number_format((float) $productWeightTotals[$d], 3) : 0,
                'total_products_foots' => isset($productFootTotals[$d]) && $productFootTotals[$d] > 0 ? number_format((float) $productFootTotals[$d], 2) : 0,
                'total_products_yards' => isset($productYardTotals[$d]) && $productYardTotals[$d] > 0 ? number_format((float) $productYardTotals[$d], 2) : 0,
                'total_products_meters' => isset($productMeterTotals[$d]) && $productMeterTotals[$d] > 0 ? number_format((float) $productMeterTotals[$d], 2) : 0,
            ]);
        }

        return $report;
    }
    public function get_daily_paid_and_due_sales($start_date, $end_date)
    {
        $salesAmounts = DB::table('sales_orders')
            ->selectRaw('
            DATE(order_date) as date,
            SUM(paid_amount) as total_paid,
            SUM(due_amount) as total_due
        ')
            ->whereBetween('order_date', [$start_date, $end_date])
            ->whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $period = new \DatePeriod(
            new Carbon($start_date),
            new \DateInterval('P1D'),
            (new Carbon($end_date))->addDay()
        );

        $report = collect();
        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            $report->push([
                'date' => $d,
                'total_paid' => $salesAmounts[$d]->total_paid ?? 0,
                'total_due' => $salesAmounts[$d]->total_due ?? 0,
            ]);
        }

        return $report;
    }
    public function get_sales_by_products($start_date, $end_date)
    {
        $sales_products = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($start_date, $end_date) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cencel')
                ->whereBetween('order_date', [$start_date, $end_date]);
        })->orderBy('qty')->get();
        return $sales_products;
    }
    public function formatNumber($num)
    {
        $n = (float) $num;
        if ($n < 1) {
            return preg_replace('/\.0+$/', '', (string) ($n * 1000));
        }

        // strip trailing .000 etc. after 3-decimal rounding
        return rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
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
