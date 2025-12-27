<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ItemType;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    function index(Request $request)
    {
        $breadcrumbs = [
            ['name' => 'Reports', 'route' => null, 'icon' => null]
        ];
        $total_products_count = Product::count();
        $total_products_value = $this->getTotalvalueOfProductsOnStore();
        $total_low_stock_products_count = Product::whereColumn('stock', '<=', 'low_stock_level')->count();
        $total_profits = $this->get_total_profit_gross();
        $proucts_count_by_items = $this->get_proucts_count_by_items();
        $proucts_count_by_brands = $this->get_proucts_count_by_brands();
        $proucts_count_by_categories = $this->get_proucts_count_by_categories();
        $proucts_count_by_sub_categories = $this->get_proucts_count_by_sub_categories();
        $total_low_stock_products = $this->get_total_low_stock_products();

        return view("pages.admin.report.base", compact('breadcrumbs', 'total_products_count', 'total_products_value', 'total_low_stock_products_count', 'total_profits', 'proucts_count_by_items', 'proucts_count_by_brands', 'proucts_count_by_categories', 'proucts_count_by_sub_categories', 'total_low_stock_products'));
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
    public function get_total_profit_gross()
    {
        $sales_orders = SalesOrder::whereNot('status', 'cancelled')
            ->whereNot('payment_status', 'cancel')
            ->with(['sales_order_products.product'])
            ->get();

        $order_total_price = $sales_orders->sum('total');

        $total_cost_price = $sales_orders->flatMap->sales_order_products
            ->sum(fn($order_product) => $order_product->product->cost_price * $order_product->qty);
        $gross_profit = $order_total_price - $total_cost_price;
        return number_format($gross_profit, 2);
    }
    public function get_proucts_count_by_items()
    {
        return ItemType::select('name')->withCount([
            'products as products_count' => function ($query) {
                $query->where('status', 'active');
            }
        ])->get();
    }
    public function get_proucts_count_by_brands()
    {
        return Brand::select('name')->withCount([
            'products as products_count' => function ($query) {
                $query->where('status', 'active');
            }
        ])->get();
    }
    public function get_proucts_count_by_categories()
    {
        return Category::select('name')->withCount([
            'products as products_count' => function ($query) {
                $query->where('status', 'active');
            }
        ])->get();
    }
    public function get_proucts_count_by_sub_categories()
    {
        return SubCategory::select('name')->withCount([
            'products as products_count' => function ($query) {
                $query->where('status', 'active');
            }
        ])->get();
    }
    public function get_total_low_stock_products()
    {
        return Product::select('id', 'name', 'stock', 'stock_w', 'stock_w_type', 'price', 'retail_price', 'image', 'item_type_id', 'brand_id', 'category_id', 'sub_category_id')
            ->when(function ($query) {
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
            })
            ->with(['item_type', 'brand', 'category', 'sub_category'])
            ->get();
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
