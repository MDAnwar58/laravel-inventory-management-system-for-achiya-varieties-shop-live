<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LowStockAlert;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index()
    {
        $this->setSettingsForAllUser();
        $this->setLowStockALertForIndividualUser();
        $salesOrderProductsUnitsCountAndPerchent = $this->getMonthlySalesOrderProductsUnitsCountAndPercent();
        $salesOrderProductsWeightsCountAndPercent = $this->getSalesOrderProductsWeightsCountAndPercent();
        $salesOrderProductsFootsCountAndPercent = $this->getSalesOrderProductsFootsCountAndPercent();
        $salesOrderProductsYardsCountAndPercent = $this->getSalesOrderProductsYardsCountAndPercent();
        $salesOrderProductsMetersCountAndPercent = $this->getSalesOrderProductsMetersCountAndPercent();
        // $salesOrderProductsCountAndPerchent = $this->getMonthlySalesOrderProductsCountAndPercent();
        $totalSalesOrderEarningsAndMonthlyEarningsIncreaseAndDecreasePercent = $this->totalSalesOrderEarningsAndMonthlyEarningsIncreaseAndDecreasePercent();
        $totalvalueOfProductsOnStore = $this->getTotalvalueOfProductsOnStore();
        $totalOrdersCountAndMonthlySalesOrdersPercentUpAndDown = $this->getTotalOrdersCountAndMonthlySalesOrdersPercentUpAndDown();
        $getMonthlySalesOrderEarnings = $this->getMonthlySalesOrderEarnings();
        $totalCustomersCount = $this->getTotalCustomersCount();
        $monthlySalesOrderProductsCount = $this->getMonthlySalesOrderProductsCount();
        $salesTop5ProductsCount = $this->getSalesTop5ProductsCount();
        $currentWeekSalesEarningsAndProductsCount = $this->getCurrentWeekSalesEarningsAndProductsCount();
        $latest_orders = SalesOrder::latest()->take(5)->get();
        $low_stock_products = $this->getLowStockProducts();

        return view('pages.admin.dashboard', compact('salesOrderProductsUnitsCountAndPerchent', 'salesOrderProductsWeightsCountAndPercent', 'salesOrderProductsFootsCountAndPercent', 'salesOrderProductsYardsCountAndPercent', 'salesOrderProductsMetersCountAndPercent', 'totalSalesOrderEarningsAndMonthlyEarningsIncreaseAndDecreasePercent', 'totalvalueOfProductsOnStore', 'getMonthlySalesOrderEarnings', 'totalOrdersCountAndMonthlySalesOrdersPercentUpAndDown', 'totalCustomersCount', 'monthlySalesOrderProductsCount', 'salesTop5ProductsCount', 'currentWeekSalesEarningsAndProductsCount', 'latest_orders', 'low_stock_products'));
    }
    public function setSettingsForAllUser()
    {
        $owner = User::where('role', 'owner')->first();
        $settings_count = Setting::count();
        if (!$settings_count > 0 && $owner->id) {
            Setting::create([
                'user_id' => $owner->id,
            ]);
        }
    }
    public function setLowStockALertForIndividualUser()
    {
        $auth_user_id = auth()->user()->id;
        $low_stock_alert = LowStockAlert::where('user_id', $auth_user_id)->first();
        if (!$low_stock_alert) {
            LowStockAlert::create([
                'user_id' => $auth_user_id,
            ]);
        }
    }
    public function getTotalvalueOfProductsOnStore()
    {
        $total_value_of_products_on_pieces = Product::where('stock_w_type', 'none')
            ->get()
            ->sum(function ($product) {
                return $product->cost_price * $product->stock;
            });
        $total_value_of_products_on_weights = Product::where('stock_w_type', 'kg')
            ->get()
            ->sum(function ($product) {
                return $product->cost_price * $product->stock_w;
            });
        $total_value_of_products_on_foots = Product::where('stock_w_type', 'ft')
            ->get()
            ->sum(function ($product) {
                return $this->calculatePrice($product->cost_price, $product->stock_w); // calculate price based on foot and inch
            });
        $total_value_of_products_on_yards = Product::where('stock_w_type', 'yard')
            ->get()
            ->sum(function ($product) {
                return $this->calculatePrice($product->cost_price, $product->stock_w, 36); // calculate price based on yard and inch
            });
        $total_value_of_products_on_metters = Product::where('stock_w_type', 'm')
            ->get()
            ->sum(function ($product) {
                return $this->calculatePrice($product->cost_price, $product->stock_w, 39); // calculate price based on yard and inch
            });


        $total_value_of_products_on_the_store = $total_value_of_products_on_pieces + $total_value_of_products_on_weights + $total_value_of_products_on_foots + $total_value_of_products_on_yards + $total_value_of_products_on_metters;

        return number_format($total_value_of_products_on_the_store, 2);
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

    public function getMonthlySalesOrderProductsUnitsCountAndPercent(): object
    {
        // Current month range
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Last month range
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Current month count
        $currentMonthTotal = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($currentMonthStart, $currentMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$currentMonthStart, $currentMonthEnd]);
        })->where('stock_w_type', 'none')->sum('qty');

        // Last month count
        $lastMonthTotal = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($lastMonthStart, $lastMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$lastMonthStart, $lastMonthEnd]);
        })->where('stock_w_type', 'none')->sum('qty');

        // Percent change
        $percent = $lastMonthTotal > 0
            ? (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100
            : 0;

        // Determine trend (up/down/flat)
        $trend = $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'flat');

        // Total sales order products count
        $total_sales_order_products_count = SalesOrderProduct::whereHas('salesOrder', function ($query) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel');
        })->where('stock_w_type', 'none')->sum('qty');

        return (object) [
            'total_sales_order_products_count' => ((int) $total_sales_order_products_count) . " pcs",
            'percent' => number_format($percent, 2),
            'trend' => $trend, // up / down / flat
        ];
    }
    public function getSalesOrderProductsWeightsCountAndPercent(): object
    {
        // Current month range
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Last month range
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Current month count
        $currentMonthTotal = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($currentMonthStart, $currentMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$currentMonthStart, $currentMonthEnd]);
        })->where('stock_w_type', 'kg')->sum('qty');

        // Last month count
        $lastMonthTotal = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($lastMonthStart, $lastMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$lastMonthStart, $lastMonthEnd]);
        })->where('stock_w_type', 'kg')->sum('qty');

        // Percent change
        $percent = $lastMonthTotal > 0
            ? (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100
            : 0;

        // Determine trend (up/down/flat)
        $trend = $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'flat');

        // Total sales order products count
        $total_sales_order_products_weight = SalesOrderProduct::whereHas('salesOrder', function ($query) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel');
        })->where('stock_w_type', 'kg')->sum('qty');

        return (object) [
            'total_sales_order_products_weight' => $this->formatNumber($total_sales_order_products_weight) . $this->kg_or_gm($total_sales_order_products_weight),
            'percent' => number_format($percent, 2),
            'trend' => $trend, // up / down / flat
        ];
    }
    public function getSalesOrderProductsFootsCountAndPercent(): object
    {
        // Current month range
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Last month range
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // 👉 Get current month sales products (feet)
        $currentMonthSalesProducts = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($currentMonthStart, $currentMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$currentMonthStart, $currentMonthEnd]);
        })->where('stock_w_type', 'ft')->get();

        // 👉 Convert to total feet+inch using custom function
        $currentMonthFoots = [];
        foreach ($currentMonthSalesProducts as $p) {
            $currentMonthFoots[] = number_format((float) $p->qty, 2);
        }
        $currentMonthTotal = $this->array_sum_any_to_inchi_calculate($currentMonthFoots);

        // 👉 Get last month sales products (feet)
        $lastMonthSalesProducts = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($lastMonthStart, $lastMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$lastMonthStart, $lastMonthEnd]);
        })->where('stock_w_type', 'ft')->get();

        // 👉 Convert to total feet+inch using custom function
        $lastMonthFoots = [];
        foreach ($lastMonthSalesProducts as $p) {
            $lastMonthFoots[] = number_format((float) $p->qty, 2);
        }
        $lastMonthTotal = $this->array_sum_any_to_inchi_calculate($lastMonthFoots);

        // 👉 Calculate percent change
        $percent = $lastMonthTotal > 0
            ? (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100
            : 0;

        // 👉 Determine trend (up/down/flat)
        $trend = $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'flat');

        // 👉 Total all-time sales products (feet)
        $totalSalesProducts = SalesOrderProduct::whereHas('salesOrder', function ($query) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel');
        })->where('stock_w_type', 'ft')->get();

        $totalFoots = [];
        foreach ($totalSalesProducts as $p) {
            $totalFoots[] = number_format((float) $p->qty, 2);
        }
        $total_foot = $this->array_sum_any_to_inchi_calculate($totalFoots);

        // 👉 Return with formatted feet/inch string
        return (object) [
            'total_sales_order_products_foot' => $this->formatNumberWithOnlyTwoDecimal($total_foot) . $this->ft_or_inchi($total_foot),
            'percent' => number_format($percent, 2),
            'trend' => $trend, // up / down / flat
        ];
    }
    public function getSalesOrderProductsYardsCountAndPercent(): object
    {
        // Current month range
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Last month range
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // 👉 Get current month sales products (feet)
        $currentMonthSalesProducts = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($currentMonthStart, $currentMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$currentMonthStart, $currentMonthEnd]);
        })->where('stock_w_type', 'yard')->get();

        // 👉 Convert to total feet+inch using custom function
        $currentMonthYards = [];
        foreach ($currentMonthSalesProducts as $p) {
            $currentMonthYards[] = number_format((float) $p->qty, 2);
        }
        $currentMonthTotal = $this->array_sum_any_to_inchi_calculate($currentMonthYards, 36);

        // 👉 Get last month sales products (feet)
        $lastMonthSalesProducts = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($lastMonthStart, $lastMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$lastMonthStart, $lastMonthEnd]);
        })->where('stock_w_type', 'yard')->get();

        // 👉 Convert to total feet+inch using custom function
        $lastMonthYards = [];
        foreach ($lastMonthSalesProducts as $p) {
            $lastMonthYards[] = number_format((float) $p->qty, 2);
        }
        $lastMonthTotal = $this->array_sum_any_to_inchi_calculate($lastMonthYards, 36);

        // 👉 Calculate percent change
        $percent = $lastMonthTotal > 0
            ? (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100
            : 0;

        // 👉 Determine trend (up/down/flat)
        $trend = $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'flat');

        // 👉 Total all-time sales products (feet)
        $totalSalesProducts = SalesOrderProduct::whereHas('salesOrder', function ($query) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel');
        })->where('stock_w_type', 'yard')->get();

        $totalYards = [];
        foreach ($totalSalesProducts as $p) {
            $totalYards[] = number_format((float) $p->qty, 2);
        }
        $total_foot = $this->array_sum_any_to_inchi_calculate($totalYards, 36);

        // 👉 Return with formatted feet/inch string
        return (object) [
            'total_sales_order_products_yard' => $this->formatNumberWithOnlyTwoDecimal($total_foot) . $this->yard_or_inchi($total_foot),
            'percent' => number_format($percent, 2),
            'trend' => $trend, // up / down / flat
        ];
    }
    public function getSalesOrderProductsMetersCountAndPercent(): object
    {
        // Current month range
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Last month range
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // 👉 Get current month sales products (feet)
        $currentMonthSalesProducts = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($currentMonthStart, $currentMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$currentMonthStart, $currentMonthEnd]);
        })->where('stock_w_type', 'm')->get();

        // 👉 Convert to total feet+inch using custom function
        $currentMonthMeters = [];
        foreach ($currentMonthSalesProducts as $p) {
            $currentMonthMeters[] = number_format((float) $p->qty, 2);
        }
        $currentMonthTotal = $this->array_sum_any_to_inchi_calculate($currentMonthMeters, 39);

        // 👉 Get last month sales products (feet)
        $lastMonthSalesProducts = SalesOrderProduct::whereHas('salesOrder', function ($query) use ($lastMonthStart, $lastMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$lastMonthStart, $lastMonthEnd]);
        })->where('stock_w_type', 'm')->get();

        // 👉 Convert to total feet+inch using custom function
        $lastMonthMeters = [];
        foreach ($lastMonthSalesProducts as $p) {
            $lastMonthMeters[] = number_format((float) $p->qty, 2);
        }
        $lastMonthTotal = $this->array_sum_any_to_inchi_calculate($lastMonthMeters, 39);

        // 👉 Calculate percent change
        $percent = $lastMonthTotal > 0
            ? (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100
            : 0;

        // 👉 Determine trend (up/down/flat)
        $trend = $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'flat');

        // 👉 Total all-time sales products (feet)
        $totalSalesProducts = SalesOrderProduct::whereHas('salesOrder', function ($query) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel');
        })->where('stock_w_type', 'm')->get();

        $totalMeters = [];
        foreach ($totalSalesProducts as $p) {
            $totalMeters[] = number_format((float) $p->qty, 2);
        }
        $total_foot = $this->array_sum_any_to_inchi_calculate($totalMeters, 39);

        // 👉 Return with formatted feet/inch string
        return (object) [
            'total_sales_order_products_meter' => $this->formatNumberWithOnlyTwoDecimal($total_foot) . $this->m_or_inchi($total_foot),
            'percent' => number_format($percent, 2),
            'trend' => $trend, // up / down / flat
        ];
    }
    public function getMonthlySalesOrderProductsCountAndPercent(): object
    {
        // Current month range
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Last month range
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Current month count
        $currentMonthTotal = Product::whereHas('sales_orders_products.salesOrder', function ($query) use ($currentMonthStart, $currentMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$currentMonthStart, $currentMonthEnd]);
        })->count();

        // Last month count
        $lastMonthTotal = Product::whereHas('sales_orders_products.salesOrder', function ($query) use ($lastMonthStart, $lastMonthEnd) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel')
                ->whereBetween('order_date', [$lastMonthStart, $lastMonthEnd]);
        })->count();

        // Percent change
        $percent = $lastMonthTotal > 0
            ? (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100
            : 0;

        // Determine trend (up/down/flat)
        $trend = $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'flat');

        // Total sales order products count
        $total_sales_order_products_count = Product::whereHas('sales_orders_products.salesOrder', function ($query) {
            $query->whereNot('status', 'cancelled')
                ->whereNot('payment_status', 'cancel');
        })->count();

        return (object) [
            'total_sales_order_products_count' => $total_sales_order_products_count,
            'percent' => number_format($percent, 2),
            'trend' => $trend, // up / down / flat
        ];
    }
    public function totalSalesOrderEarningsAndMonthlyEarningsIncreaseAndDecreasePercent(): object
    {
        // Total earnings (all time)
        $totalEarnings = SalesOrder::whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')->sum('total'); // or sum('discount_price') if you use discounts

        // Current month range
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Last month range
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Current month earnings
        $currentMonthEarnings = SalesOrder::whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->whereBetween('order_date', [$currentMonthStart, $currentMonthEnd])
            ->sum('total'); // adjust column if needed

        // Last month earnings
        $lastMonthEarnings = SalesOrder::whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->whereBetween('order_date', [$lastMonthStart, $lastMonthEnd])->sum('total');

        // Calculate percent change
        $percent = $lastMonthEarnings > 0
            ? (($currentMonthEarnings - $lastMonthEarnings) / $lastMonthEarnings) * 100
            : 0;

        // Determine trend
        $trend = $percent > 0 ? 'up' : ($percent < 0 ? 'down' : 'flat');

        // 'current_month' => $currentMonthEarnings,
        // 'last_month' => $lastMonthEarnings,
        return (object) [
            'total_earnings' => number_format($totalEarnings, 2),
            'percent' => number_format($percent, 2),
            'trend' => $trend,
        ];
    }
    public function getMonthlySalesOrderEarnings(): array
    {
        // Initialize months array with short names
        $months = collect(range(1, 12))->mapWithKeys(function ($month) {
            return [
                $month => [
                    'month' => Carbon::create()->month($month)->format('M'), // short month
                    'earnings' => 0
                ]
            ];
        });

        // Fetch sales grouped by month
        $sales = DB::table('sales_orders')
            ->selectRaw('MONTH(order_date) as month, SUM(total) as earnings')
            ->whereYear('order_date', date('Y')) // current year
            ->whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->groupBy('month')
            ->get();

        // Merge earnings into months array safely
        foreach ($sales as $sale) {
            $months->put($sale->month, [
                'month' => $months[$sale->month]['month'],
                'earnings' => (float) $sale->earnings,
            ]);
        }

        // Return as array
        return $months->values()->toArray();
    }
    public function getTotalOrdersCountAndMonthlySalesOrdersPercentUpAndDown()
    {
        $sales_orders_count = SalesOrder::whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->count();

        $now = Carbon::now();
        $startCurrent = $now->copy()->startOfMonth();
        $endCurrent = $now->copy()->endOfMonth();
        $startPrevious = $now->copy()->subMonth()->startOfMonth();
        $endPrevious = $now->copy()->subMonth()->endOfMonth();

        $current = DB::table('sales_orders')
            ->whereBetween('order_date', [$startCurrent, $endCurrent])
            ->whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->count();

        $previous = DB::table('sales_orders')
            ->whereBetween('order_date', [$startPrevious, $endPrevious])
            ->whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->count();

        $percentage = 0.0;
        $trend = 'flat';

        if ($previous > 0) {
            $percentage = round((($current - $previous) / $previous) * 100, 2);
            // $percentage = min($percentage, 100); // cap at 100%
        } elseif ($previous == 0 && $current > 0) {
            $percentage = 0; // cap at 100
        }

        if ($percentage > 0) {
            $trend = 'up';
        } elseif ($percentage < 0) {
            $trend = 'down';
        }

        // Return as array
        // 'current_month_count' => $current,
        // 'previous_month_count' => $previous,
        return (object) [
            'sales_orders_count' => $sales_orders_count,
            'percentage_change' => number_format($percentage, 2),
            'trend' => $trend,
        ];
    }
    public function getTotalCustomersCount(): int
    {
        $customers_count = Customer::count();
        return $customers_count;
    }
    public function getMonthlySalesOrderProductsCount(): array
    {
        $now = Carbon::now();

        // Query current year grouped by sales order date
        $data = SalesOrderProduct::select(
            DB::raw('SUM(sales_order_products.qty) as total'), // aggregated sum
            DB::raw('MONTH(sales_orders.order_date) as month'),
            DB::raw('YEAR(sales_orders.order_date) as year'),
            'sales_order_products.stock_w_type'
        )
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_products.sales_order_id')
            ->whereYear('sales_orders.order_date', $now->year) // only this year
            ->whereNot('sales_orders.status', 'cancelled')
            ->whereNot('sales_orders.payment_status', 'cancel')
            ->groupBy('year', 'month', 'sales_order_products.stock_w_type')
            ->orderBy('month')
            ->get();

        // Prepare result Jan → Dec
        $result = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthName = Carbon::create()->month($m)->format('M');

            // find rows for this month (collection filtering)
            $monthData = $data->where('month', $m)->where('year', $now->year);

            // split qty & weight based on stock_w_type
            $qtyTotal = (int) $monthData->where('stock_w_type', 'none')->sum('total');
            $weightTotal = (float) $monthData->where('stock_w_type', 'kg')->sum('total');

            // feet needs special summing (ft may be stored as feet.inch or similar)
            $total_foots = [];
            $sales_products_ft = $monthData->where('stock_w_type', 'ft'); // collection of aggregated rows for ft

            foreach ($sales_products_ft as $p) {
                // note: query returned 'total' (SUM(qty)), so use $p->total
                $total_foots[] = number_format((float) $p->total, 2);
            }

            $feetTotal = count($total_foots) > 0
                ? $this->array_sum_any_to_inchi_calculate($total_foots)
                : 0;

            // yard needs special summing (ft may be stored as yard.inch or similar)
            $total_yards = [];
            $sales_products_yard = $monthData->where('stock_w_type', 'yard'); // collection of aggregated rows for yard

            foreach ($sales_products_yard as $p) {
                // note: query returned 'total' (SUM(qty)), so use $p->total
                $total_yards[] = number_format((float) $p->total, 2);
            }

            $yardTotal = count($total_yards) > 0
                ? $this->array_sum_any_to_inchi_calculate($total_yards, 36)
                : 0;

            // meter needs special summing (ft may be stored as meter.inch or similar)
            $total_meters = [];
            $sales_products_meter = $monthData->where('stock_w_type', 'm'); // collection of aggregated rows for yard

            foreach ($sales_products_meter as $p) {
                // note: query returned 'total' (SUM(qty)), so use $p->total
                $total_meters[] = number_format((float) $p->total, 2);
            }

            $meterTotal = count($total_meters) > 0
                ? $this->array_sum_any_to_inchi_calculate($total_meters, 39)
                : 0;

            $result[] = [
                'month' => $monthName,
                'count' => $qtyTotal,
                'weights_count' => $weightTotal,
                'feets_count' => $feetTotal,
                'yard_count' => $yardTotal,
                'meter_count' => $meterTotal,
            ];
        }

        return $result;
    }

    public function getSalesTop5ProductsCount(): array
    {
        $data = SalesOrderProduct::select(
            'products.name',
            DB::raw('SUM(sales_order_products.qty) as total_qty'),
            'sales_order_products.stock_w_type'
        )
            ->join('products', 'products.id', '=', 'sales_order_products.product_id')
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_products.sales_order_id')
            ->whereNot('sales_orders.status', 'cancelled')
            ->whereNot('sales_orders.payment_status', 'cancel')
            ->groupBy('products.id', 'products.name', 'sales_order_products.stock_w_type')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return $data->toArray();
    }
    public function getCurrentWeekSalesEarningsAndProductsCount(): array
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek(); // Monday
        $endOfWeek = $now->copy()->endOfWeek();     // Sunday

        // Prepare days of the current week with default values
        $days = collect(range(0, 6))->mapWithKeys(function ($offset) use ($startOfWeek) {
            $date = $startOfWeek->copy()->addDays($offset);
            return [
                $date->day => [
                    'day' => $date->format('D d M'), // e.g. Mon 23 Sep
                    'earnings' => 0,
                    'products_count' => 0,   // pcs
                    'weight_count' => 0.0,   // kg
                    'feet_count' => 0.0,   // feet
                    'yard_count' => 0.0,   // feet
                    'meter_count' => 0.0,   // feet
                ]
            ];
        });

        // Fetch sales grouped by day
        $sales = DB::table('sales_orders')
            ->join('sales_order_products', 'sales_order_products.sales_order_id', '=', 'sales_orders.id')
            ->selectRaw('DAY(sales_orders.order_date) as day,
                     SUM(sales_orders.total) as earnings,
                     SUM(CASE WHEN sales_order_products.stock_w_type = "none" THEN sales_order_products.qty ELSE 0 END) as quantity_count,
                     SUM(CASE WHEN sales_order_products.stock_w_type = "kg" THEN sales_order_products.qty ELSE 0 END) as weight_count,
                     SUM(CASE WHEN sales_order_products.stock_w_type = "ft" THEN sales_order_products.qty ELSE 0 END) as feet_count,
                     SUM(CASE WHEN sales_order_products.stock_w_type = "yard" THEN sales_order_products.qty ELSE 0 END) as yard_count,
                     SUM(CASE WHEN sales_order_products.stock_w_type = "m" THEN sales_order_products.qty ELSE 0 END) as meter_count'
            )
            ->whereBetween('sales_orders.order_date', [$startOfWeek, $endOfWeek])
            ->whereNot('sales_orders.status', 'cancelled')
            ->whereNot('sales_orders.payment_status', 'cancel')
            ->groupBy('day')
            ->get();

        // Merge sales data into days collection
        foreach ($sales as $sale) {
            if (isset($days[$sale->day])) {
                $days->put($sale->day, [
                    'day' => $days[$sale->day]['day'],
                    'earnings' => (float) $sale->earnings,
                    'products_count' => (int) $sale->quantity_count,
                    'weight_count' => (float) $sale->weight_count,
                    'feet_count' => (float) $sale->feet_count,
                    'yard_count' => (float) $sale->yard_count,
                    'meter_count' => (float) $sale->meter_count,
                ]);
            }
        }

        return $days->values()->toArray();
    }


    public function getLowStockProducts(): mixed
    {
        $products = Product::when(function ($query) {
            // If stock_w_type is 'none' compare 'stock', else compare 'stock_w'
            $query->where(function ($q) {
                $q->where('stock_w_type', 'none')
                    ->whereColumn('stock', '<=', 'low_stock_level')
                    ->orderBy('stock', 'desc');
            })->orWhere(function ($q) {
                $q->where('stock_w_type', '!=', 'none')
                    ->whereColumn('stock_w', '<=', 'low_stock_level')
                    ->orderBy('stock_w', 'desc');
            });
        })->with(['item_type', 'brand', 'category', 'sub_category'])->get();
        return $products;
    }
    private function formatNumber($num)
    {
        $n = (float) $num;
        if ($n < 1) {
            return preg_replace('/\.0+$/', '', (string) ($n * 1000));
        }

        // strip trailing .000 etc. after 3-decimal rounding
        return rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
    }
    private function kg_or_gm($num)
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
    public function formatNumberWithOnlyTwoDecimal($num)
    {
        $n = (float) $num;
        if ($n < 1)
            $repl = (string) intval(round($n * 1000 / 10));
        else
            $repl = rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        return $repl;
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
