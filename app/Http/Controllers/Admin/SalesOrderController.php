<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerStoreRequest;
use App\Http\Requests\Admin\CustomerUpdateRequest;
use App\Http\Requests\Admin\SalesOrderStoreRequest;
use App\Http\Requests\Admin\SalesOrderUpdateRequest;
use App\Models\Customer;
use App\Models\PrintingContent;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderProduct;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

class SalesOrderController extends Controller
{
    public function get(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');
        $payment_status_filter = $request->input('payment_status_filter', '');
        $sortColumn = $request->input('sort_column', 'created_at');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', 5);

        $query = SalesOrder::query()->with('customer', 'user', 'sales_order_products.product');

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%");
                $q->orWhere('memo_no', 'like', "%{$search}%");
                $q->orWhereHas('customer', function ($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', "%{$search}%");
                    $customerQuery->orWhere('phone', 'like', "%{$search}%");
                    $customerQuery->orWhere('address', 'like', "%{$search}%");
                });
            });
        });

        $query->when($filter, function ($query) use ($filter) {
            $query->where('status', $filter);
        });

        $query->when($payment_status_filter, function ($query) use ($payment_status_filter) {
            $query->where('payment_status', $payment_status_filter);
        });

        $query->when($sortColumn && $sort, function ($query) use ($sortColumn, $sort) {
            $query->orderBy($sortColumn, $sort);
        });

        if (!$search && !$filter && !$sortColumn && !$sort)
            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        else
            return $query->paginate($perPage);
    }
    public function get_products(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);

        $query = Product::query()->select('id', 'name', 'price', 'discount_price', 'image', 'stock');

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });

        if (!$search)
            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        else
            return $query->paginate($perPage);
    }
    public function get_customers(Request $request)
    {
        $search = $request->input('search', '');
        $sortColumn = $request->input('sort_column', 'created_at');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', 10);

        $query = Customer::query()->select('id', 'name', 'phone', 'address');

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
                $q->orWhere('phone', 'like', "%{$search}%");
            });
        });

        $query->when($sortColumn && $sort, function ($query) use ($sortColumn, $sort) {
            $query->orderBy($sortColumn, $sort);
        });

        if (!$search && !$sortColumn && !$sort)
            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        else
            return $query->paginate($perPage);
    }
    public function get_products_for_card(Request $request)
    {
        $id = $request->input('id', '');
        $product = Product::select('id', 'name', 'price', 'discount_price', 'retail_price', 'purchase_limit', 'image', 'stock', 'stock_w', 'stock_w_type', 'tax')->where('id', intval($id))->first();
        return $product;
        // return Product::all(['id', 'name', 'price', 'discount_price', 'retail_price', 'purchase_limit', 'image', 'stock', 'stock_w', 'stock_w_type', 'tax']);
    }
    public function add_new_customer(CustomerStoreRequest $request)
    {
        $validated = $request->validated();

        $customer = new Customer();
        $customer->name = $validated['name'];
        $customer->phone = $validated['phone'];
        $customer->address = $validated['address'];
        $customer->save();

        return response()->json([
            'status' => 'success',
            'msg' => 'Customer created!'
        ]);
    }

    public function index(): View
    {
        $theadColumns = [
            ['name' => 'Serial No.', 'sortable' => false],
            ['name' => 'Order No.', 'sortable' => true, 'sortable_col' => 'order_number'],
            ['name' => 'Memo No.', 'sortable' => true, 'sortable_col' => 'memo_no'],
            ['name' => 'Customer', 'sortable' => false],
            ['name' => 'Phone', 'sortable' => false],
            ['name' => 'Address', 'sortable' => false],
            ['name' => 'Paid Amount', 'sortable' => true, 'sortable_col' => 'paid_amount'],
            ['name' => 'Due Amount', 'sortable' => true, 'sortable_col' => 'due_amount'],
            ['name' => 'Total', 'sortable' => true, 'sortable_col' => 'total'],
            ['name' => 'Order Date', 'sortable' => true, 'sortable_col' => 'order_date'],
            ['name' => 'Due Date', 'sortable' => true, 'sortable_col' => 'due_date'],
            ['name' => 'Order Cancelled Date', 'sortable' => true, 'sortable_col' => 'due_date'],
            ['name' => 'Products', 'sortable' => false],
            ['name' => 'Payment Status', 'sortable' => false],
            ['name' => 'Status', 'sortable' => false],
            ['name' => 'Actions', 'sortable' => false],
        ];

        $breadcrumbs = [
            ['name' => 'Sales Orders', 'route' => null, 'icon' => null]
        ];
        $setting = Setting::first();
        return view('pages.admin.sales-order.base', compact('theadColumns', 'breadcrumbs', 'setting'));
    }
    public function show($id): View
    {
        $data = SalesOrder::where('id', $id)->with('customer', 'sales_order_products.product')->first();
        $printing_content = PrintingContent::first();

        $breadcrumbs = [
            ['name' => 'Sales Orders', 'route' => 'admin.sales.orders', 'icon' => null],
            ['name' => 'Invoice', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.sales-order.show', compact('data', 'breadcrumbs', 'printing_content'));
    }
    public function create()
    {
        $breadcrumbs = [
            ['name' => 'Sales Orders', 'route' => 'admin.sales.orders', 'icon' => null],
            ['name' => 'Create', 'route' => null, 'icon' => null]
        ];
        $printing_content = PrintingContent::first();

        return view('pages.admin.sales-order.create', compact('breadcrumbs', 'printing_content'));
    }
    public function store(SalesOrderStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $products = json_decode($validated['products'], true);

            if (!count($products) > 0)
                return back()->with([
                    'status' => 'warning',
                    'msg' => 'Please Select Products!'
                ]);
            $owner = User::where('role', 'owner')->first();

            $order_number = 'SO-' . rand(1000, 9999) . '-' . date('dm') . '-' . date('His');
            $sales_order = new SalesOrder();
            if ($owner)
                $sales_order->user_id = $owner->id;
            $sales_order->customer_id = $validated['customer_id'];
            $sales_order->order_number = $order_number;
            $sales_order->order_date = $validated['order_date'];
            if (array_key_exists('due_date', $validated) && $validated['due_date'])
                $sales_order->due_date = $validated['due_date'];
            $sales_order->payment_status = $validated['payment_status'];
            $sales_order->memo_no = $validated['memo_no'];
            $sales_order->notes = $validated['notes'];
            $sales_order->sub_total = $validated['sub_total_amount'];
            $sales_order->total = $validated['total_amount'];
            if (array_key_exists('paid_amount', $validated) && $validated['paid_amount'])
                $sales_order->paid_amount = $validated['paid_amount'];
            else
                $sales_order->paid_amount = $validated['total_amount'];

            if ($validated['payment_status'] !== "paid") {
                if (array_key_exists('due_amount', $validated) && $validated['due_amount'])
                    $sales_order->due_amount = $validated['due_amount'];
            }
            $sales_order->save();

            foreach ($products as $product) {
                $sales_order_item = new SalesOrderProduct();
                $sales_order_item->sales_order_id = $sales_order->id;

                if ($owner)
                    $sales_order_item->user_id = $owner->id;
                $sales_order_item->customer_id = $validated['customer_id'];
                $sales_order_item->product_id = $product['id'];
                $sales_order_item->qty = $product['qty'];
                $sales_order_item->price = $product['price'];
                $sales_order_item->discount_price = $product['discount_price'];
                $sales_order_item->total = $product['total_price'];
                $sales_order_item->retail_price_status = $product['retail_price_status'];
                $sales_order_item->stock_w_type = $product['stock_w_type'];
                $sales_order_item->save();

                $product = Product::findOrFail(intval($sales_order_item->product_id));
                $stock_updated = 'Last solded ' . $sales_order_item->qty . ' units';
                $currentDateTime = now()->format('Y-m-d H:i:s');
                $product->sold_units = $stock_updated;
                $product->solded_at = $currentDateTime;
                if ($sales_order_item->stock_w_type !== 'none') {
                    if ($sales_order_item->stock_w_type === 'ft') {
                        $product->stock_w = $this->calculateFtAndInchiDecrease((float) $product->stock_w, (float) $sales_order_item->qty);
                    } elseif ($sales_order_item->stock_w_type === 'yard') {
                        $product->stock_w = $this->calculateFtAndInchiDecrease((float) $product->stock_w, (float) $sales_order_item->qty, 36);
                    } elseif ($sales_order_item->stock_w_type === 'm') {
                        $product->stock_w = $this->calculateFtAndInchiDecrease((float) $product->stock_w, (float) $sales_order_item->qty, 39);
                    } else
                        $product->stock_w -= $sales_order_item->qty;
                } else
                    $product->stock -= $sales_order_item->qty;
                $product->save();

                if ($product->stock <= $product->low_stock_level)
                    Cache::put('low_stock_alert', true);
                else
                    Cache::forget('low_stock_alert');
            }
            DB::commit();
            return redirect()
                ->route('admin.sales.orders')
                ->with([
                    'status' => 'success',
                    'msg' => 'Sales Order created!'
                ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with([
                    'status' => 'error',
                    'msg' => 'Something went wrong. Please try again.'
                ]);
        }
    }
    public function edit($id): View
    {
        $data = SalesOrder::where('id', $id)->with('customer', 'sales_order_products.product')->first();
        $printing_content = PrintingContent::first();
        $breadcrumbs = [
            ['name' => 'Sales Orders', 'route' => 'admin.sales.orders', 'icon' => null],
            ['name' => 'Edit', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.sales-order.edit', compact('data', 'breadcrumbs', 'printing_content'));
    }
    public function update(SalesOrderUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $products = json_decode($validated['products'], true);
            if (!count($products) > 0)
                return back()->with([
                    'status' => 'warning',
                    'msg' => 'Please Select Products!'
                ]);


            $owner = User::where('role', 'owner')->first();
            $order_number = 'SO-' . rand(1000, 9999) . '-' . date('dm') . '-' . date('His');

            $sales_order = SalesOrder::findOrFail(intval($id));
            if (!$sales_order)
                return redirect()->back()->with([
                    'status' => 'warning',
                    'msg' => 'Sales Order not found!'
                ]);

            $order_status = $sales_order->status;

            if ($owner)
                $sales_order->user_id = $owner->id;
            $sales_order->customer_id = $validated['customer_id'];
            $sales_order->order_number = $order_number;
            $sales_order->order_date = $validated['order_date'];
            $sales_order->memo_no = $validated['memo_no'];
            // due date logic
            if ($validated['status'] === "cancelled" || ($validated['status'] === "confirmed" && $validated['payment_status'] === "paid")) {
                $sales_order->due_date = null;
            } elseif (array_key_exists('due_date', $validated) && $validated['due_date']) {
                $sales_order->due_date = $validated['due_date'];
            } else {
                $sales_order->due_date = null;
            }

            // payment status logic
            if (array_key_exists('status', $validated) && array_key_exists('payment_status', $validated) && $validated['status'] === "cancelled" && $validated['payment_status'] !== "cancel")
                $sales_order->payment_status = "cancel";
            else
                $sales_order->payment_status = $validated['payment_status'];

            $sales_order->status = $validated['status'];
            $sales_order->notes = $validated['notes'];
            $sales_order->sub_total = $validated['sub_total_amount'];
            $sales_order->total = $validated['total_amount'];

            // cancelled date null logic
            if ($validated['status'] !== "cancelled" || ($validated['status'] !== "confirmed" && $validated['payment_status'] === "paid")) {
                $sales_order->cancelled_date = null;
            }

            // paid amount logic
            if ($validated['status'] === "cancelled") {
                $currentDateTime = now()->format('Y-m-d H:i:s');
                $sales_order->cancelled_date = $currentDateTime;
                $sales_order->paid_amount = 0.00;
            } elseif (array_key_exists('paid_amount', $validated) && $validated['paid_amount']) {
                $sales_order->paid_amount = $validated['paid_amount'];
            } else {
                $sales_order->paid_amount = $validated['total_amount'];
            }
            // $sales_order->paid_amount = array_key_exists('paid_amount', $validated) && $validated['paid_amount'] ? $validated['paid_amount'] : $validated['total_amount'];
            // $sales_order->due_amount = array_key_exists('due_amount', $validated) && $validated['due_amount'] ? $validated['due_amount'] : 0.00;
            // due amount logic
            if ($validated['status'] === "cancelled") {
                $sales_order->due_amount = 0.00;
            } elseif (array_key_exists('due_amount', $validated) && $validated['due_amount']) {
                if ($validated['status'] === "confirmed" && $validated['payment_status'] === "paid")
                    $sales_order->due_amount = 0.00;
                else
                    $sales_order->due_amount = $validated['due_amount'];
            } else {
                $sales_order->due_amount = 0.00;
            }

            $sales_order->save();

            $product_ids = array_column($products, 'id');
            $get_in_products = SalesOrderProduct::where('sales_order_id', $id)
                ->whereNotIn('product_id', $product_ids)
                ->get();

            if ($get_in_products) {
                foreach ($get_in_products as $old_order_product) {
                    $product = Product::findOrFail(intval($old_order_product->product_id));
                    if ($product->stock_w_type !== 'none') {
                        if ($product->stock_w_type === 'ft')
                            $product->stock_w = $this->calculateFtAndInchiIncrease(number_format((float) $product->stock_w, 2), number_format((float) $old_order_product->qty, 2));
                        elseif ($product->stock_w_type === 'yard')
                            $product->stock_w = $this->calculateFtAndInchiIncrease(number_format((float) $product->stock_w, 2), number_format((float) $old_order_product->qty, 2), 36);
                        elseif ($product->stock_w_type === 'm')
                            $product->stock_w = $this->calculateFtAndInchiIncrease(number_format((float) $product->stock_w, 2), number_format((float) $old_order_product->qty, 2), 39);
                        else
                            $product->stock_w += $old_order_product->qty;
                    } else
                        $product->stock += $old_order_product->qty;
                    $product->save();
                    $old_order_product->delete();

                    if ($product->stock <= $product->low_stock_level)
                        Cache::put('low_stock_alert', true);
                    else
                        Cache::forget('low_stock_alert');
                }
            }

            foreach ($products as $product) {
                $sales_order_item = SalesOrderProduct::where('sales_order_id', $sales_order->id)
                    ->where('product_id', $product['id'])
                    ->first();
                if ($sales_order_item) {
                    $p = Product::findOrFail(intval($sales_order_item->product_id));

                    $kg = $sales_order_item->stock_w_type === 'kg';
                    $ft = $sales_order_item->stock_w_type === 'ft';
                    $yard = $sales_order_item->stock_w_type === 'yard';
                    $m = $sales_order_item->stock_w_type === 'm';


                    // if status is not cancelled and payment_status is not cancel
                    // this time stock increase and decrease
                    if (
                        array_key_exists('status', $validated)
                        && $validated['status'] !== "cancelled"
                        && array_key_exists('payment_status', $validated)
                        && $validated['payment_status'] !== "cancel"
                        || array_key_exists('status', $validated)
                        && $validated['status'] !== "cancelled"
                    ) {
                        if ($product['qty'] > $sales_order_item->qty) {
                            if ($kg) {
                                $p->stock_w -= $product['qty'] - (float) $sales_order_item->qty;
                            } elseif ($ft) {
                                // return $product['qty'] . " - " . number_format((float) $sales_order_item->qty, 2);
                                $decreaseQty = $this->calculateFtAndInchiDecreaseInProductStockQty(number_format((float) $product['qty'], 2), number_format((float) $sales_order_item->qty, 2));
                                if ($decreaseQty > number_format((float) $p->stock_w, 2)) {
                                    $p->stock_w = $this->calculateFtAndInchiDecrease($decreaseQty, number_format((float) $p->stock_w, 2));
                                } else {
                                    $p->stock_w = $this->calculateFtAndInchiDecrease(number_format((float) $p->stock_w, 2), $decreaseQty);
                                }
                            } elseif ($yard) {
                                // return $product['qty'] . " - " . number_format((float) $sales_order_item->qty, 2);
                                $decreaseQty = $this->calculateFtAndInchiDecreaseInProductStockQty(number_format((float) $product['qty'], 2), number_format((float) $sales_order_item->qty, 2), 36);
                                if ($decreaseQty > number_format((float) $p->stock_w, 2)) {
                                    $p->stock_w = $this->calculateFtAndInchiDecrease($decreaseQty, number_format((float) $p->stock_w, 2), 36);
                                } else {
                                    $p->stock_w = $this->calculateFtAndInchiDecrease(number_format((float) $p->stock_w, 2), $decreaseQty, 36);
                                }
                            } elseif ($m) {
                                // return $product['qty'] . " - " . number_format((float) $sales_order_item->qty, 2);
                                $decreaseQty = $this->calculateFtAndInchiDecreaseInProductStockQty(number_format((float) $product['qty'], 2), number_format((float) $sales_order_item->qty, 2), 39);
                                if ($decreaseQty > number_format((float) $p->stock_w, 2)) {
                                    $p->stock_w = $this->calculateFtAndInchiDecrease($decreaseQty, number_format((float) $p->stock_w, 2), 39);
                                } else {
                                    $p->stock_w = $this->calculateFtAndInchiDecrease(number_format((float) $p->stock_w, 2), $decreaseQty, 39);
                                }
                            } else {
                                $p->stock -= $product['qty'] - (int) $sales_order_item->qty;
                            }

                            $p->save();
                        } else {
                            // if (!$yard) {
                            //     return number_format((float) $product['qty'], 2) . "-" . number_format((float) $sales_order_item->qty, 2);
                            // }
                            if ($kg)
                                $p->stock_w += (float) $sales_order_item->qty - $product['qty'];
                            elseif ($ft) {
                                $decreaseQty = $this->calculateFtAndInchiDecreaseInProductStockQty(number_format((float) $sales_order_item->qty, 2), number_format((float) $product['qty'], 2));
                                $p->stock_w = $this->calculateFtAndInchiIncrease(number_format((float) $p->stock_w, 2), $decreaseQty);
                            } elseif ($yard) {
                                $decreaseQty = $this->calculateFtAndInchiDecreaseInProductStockQty(number_format((float) $sales_order_item->qty, 2), number_format((float) $product['qty'], 2), 36);
                                $p->stock_w = $this->calculateFtAndInchiIncrease(number_format((float) $p->stock_w, 2), $decreaseQty, 36);
                            } elseif ($m) {
                                $decreaseQty = $this->calculateFtAndInchiDecreaseInProductStockQty(number_format((float) $sales_order_item->qty, 2), number_format((float) $product['qty'], 2), 39);
                                $p->stock_w = $this->calculateFtAndInchiIncrease(number_format((float) $p->stock_w, 2), $decreaseQty, 39);
                            } else
                                $p->stock += (int) $sales_order_item->qty - $product['qty'];
                            $p->save();
                        }
                    }
                    if (
                        array_key_exists('status', $validated)
                        && $validated['status'] === "cancelled"
                        && array_key_exists('payment_status', $validated)
                        && $validated['payment_status'] === "cancel"
                        || array_key_exists('status', $validated)
                        && $validated['status'] === "cancelled"
                    ) {
                        if ($kg)
                            $p->stock_w += (float) $sales_order_item->qty;
                        elseif ($ft)
                            $p->stock_w = $this->calculateFtAndInchiIncrease(number_format((float) $p->stock_w, 2), number_format((float) $sales_order_item->qty, 2));
                        elseif ($yard)
                            $p->stock_w = $this->calculateFtAndInchiIncrease(number_format((float) $p->stock_w, 2), number_format((float) $sales_order_item->qty, 2), 36);
                        elseif ($m)
                            $p->stock_w = $this->calculateFtAndInchiIncrease(number_format((float) $p->stock_w, 2), number_format((float) $sales_order_item->qty, 2), 39);
                        else
                            $p->stock += (int) $sales_order_item->qty;
                        $p->save();
                    } else {
                        if ($order_status === "cancelled") {
                            if ($kg)
                                $p->stock_w -= (float) $sales_order_item->qty;
                            elseif ($ft)
                                $p->stock_w = $this->calculateFtAndInchiDecrease(number_format((float) $p->stock_w, 2), number_format((float) $sales_order_item->qty, 2));
                            elseif ($yard)
                                $p->stock_w = $this->calculateFtAndInchiDecrease(number_format((float) $p->stock_w, 2), number_format((float) $sales_order_item->qty, 2), 36);
                            elseif ($m)
                                $p->stock_w = $this->calculateFtAndInchiDecrease(number_format((float) $p->stock_w, 2), number_format((float) $sales_order_item->qty, 2), 39);
                            else
                                $p->stock -= (int) $sales_order_item->qty;
                            $p->save();
                        }
                    }
                    if ($p->stock <= $p->low_stock_level)
                        Cache::put('low_stock_alert', true);
                    else
                        Cache::forget('low_stock_alert');

                    $sales_order_item->customer_id = $validated['customer_id'];
                    $sales_order_item->qty = $product['qty'];
                    $sales_order_item->price = $product['price'];
                    $sales_order_item->discount_price = $product['discount_price'];
                    $sales_order_item->total = $product['total_price'];
                    $sales_order_item->stock_w_type = $product['stock_w_type'];
                    $sales_order_item->retail_price_status = $product['retail_price_status'];
                    $sales_order_item->save();
                } else {
                    $sales_order_item = new SalesOrderProduct();
                    $sales_order_item->sales_order_id = $sales_order->id;

                    if ($owner)
                        $sales_order_item->user_id = $owner->id;
                    $sales_order_item->customer_id = $validated['customer_id'];
                    $sales_order_item->product_id = $product['id'];
                    $sales_order_item->qty = $product['qty'];
                    $sales_order_item->price = $product['price'];
                    $sales_order_item->discount_price = $product['discount_price'];
                    $sales_order_item->total = $product['total_price'];
                    $sales_order_item->stock_w_type = $product['stock_w_type'];
                    $sales_order_item->retail_price_status = $product['retail_price_status'];
                    $sales_order_item->save();

                    $kg = $sales_order_item->stock_w_type === 'kg';
                    $ft = $sales_order_item->stock_w_type === 'ft';
                    $yard = $sales_order_item->stock_w_type === 'yard';
                    $m = $sales_order_item->stock_w_type === 'm';
                    $p = Product::findOrFail(intval($sales_order_item->product_id));
                    if ($kg)
                        $p->stock_w -= (float) $sales_order_item->qty;
                    elseif ($ft)
                        $p->stock_w = $this->calculateFtAndInchiDecrease(number_format((float) $p->stock_w, 2), number_format((float) $sales_order_item->qty, 2));
                    elseif ($yard)
                        $p->stock_w = $this->calculateFtAndInchiDecrease(number_format((float) $p->stock_w, 2), number_format((float) $sales_order_item->qty, 2), 36);
                    elseif ($m)
                        $p->stock_w = $this->calculateFtAndInchiDecrease(number_format((float) $p->stock_w, 2), number_format((float) $sales_order_item->qty, 2), 39);
                    else
                        $p->stock -= (int) $sales_order_item->qty;
                    $p->save();

                    if ($p->stock <= $p->low_stock_level)
                        Cache::put('low_stock_alert', true);
                    else
                        Cache::forget('low_stock_alert');
                }
            }
            DB::commit();
            return redirect()
                ->route('admin.sales.orders')
                ->with([
                    'status' => 'success',
                    'msg' => 'Sales Order updated!'
                ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with([
                    'status' => 'error',
                    'msg' => 'Something went wrong. Please try again.'
                ]);
        }
    }
    public function destroy($id)
    {
        $sales_order = SalesOrder::findOrFail(intval($id));
        if (!$sales_order)
            return response()->json([
                'status' => 'warning',
                'msg' => 'Sales Order not found!'
            ]);

        $sales_order->delete();
        return response()->json([
            'status' => 'success',
            'msg' => 'Sales Order deleted!'
        ]);
    }
    public function calculateFtAndInchiDecrease($stock, $qty, $inchi = 12)
    {
        // Parse stock
        $s = explode('.', string: (string) $stock);
        $sFt = (int) $s[0];
        $sInch = isset($s[1]) ? (int) str_pad($s[1], 2, '0', STR_PAD_RIGHT) : 0;

        // Parse qty
        $q = explode('.', (string) $qty);
        $qFt = (int) $q[0];
        $qInch = isset($q[1]) ? (int) str_pad($q[1], 2, '0', STR_PAD_RIGHT) : 0;

        // Convert to total inches
        $stockInches = $sFt * $inchi + $sInch;
        $qtyInches = $qFt * $inchi + $qInch;

        // Subtract in inches
        $remainingInches = $stockInches - $qtyInches;
        if ($remainingInches < 0)
            $remainingInches = 0;

        // Convert back to feet and inches
        $ft = floor($remainingInches / $inchi);
        $inch = $remainingInches % $inchi;

        // Return as feet.inch (2 digits for inches)
        return (float) ($ft . '.' . str_pad($inch, 2, '0', STR_PAD_LEFT));
    }
    public function calculateFtAndInchiIncrease($stock, $qty, $inchi = 12)
    {
        // Parse stock
        $s = explode('.', (string) $stock);
        $sFt = (int) $s[0];
        $sInch = isset($s[1]) ? (int) str_pad($s[1], 2, '0', STR_PAD_LEFT) : 0;

        // Parse qty
        $q = explode('.', (string) $qty);
        $qFt = (int) $q[0];
        $qInch = isset($q[1]) ? (int) str_pad($q[1], 2, '0', STR_PAD_LEFT) : 0;

        // Convert to total inches
        $stockInches = $sFt * $inchi + $sInch;
        $qtyInches = $qFt * $inchi + $qInch;

        // Add inches
        $totalInches = $stockInches + $qtyInches;

        // Convert back to feet and inches
        $ft = floor($totalInches / $inchi);
        $inch = $totalInches % $inchi;

        // Format result with 2 digits for inch
        return $ft . '.' . str_pad($inch, 2, '0', STR_PAD_LEFT);
    }
    public function calculateFtAndInchiDecreaseInProductStockQty($qty_big, $qty_small, $inchi = 12)
    {
        $s = explode('.', (string) $qty_big);
        $sFt = (int) $s[0];
        $sInch = isset($s[1]) ? (int) str_pad($s[1], 2, '0', STR_PAD_LEFT) : 0;

        $q = explode('.', (string) $qty_small);
        $qFt = (int) $q[0];
        $qInch = isset($q[1]) ? (int) str_pad($q[1], 2, '0', STR_PAD_LEFT) : 0;

        $stockInches = $sFt * $inchi + $sInch;
        $qtyInches = $qFt * $inchi + $qInch;

        $remainingInches = 0;
        if ($stockInches > $qtyInches)
            $remainingInches = $stockInches - $qtyInches;
        else
            $remainingInches = $qtyInches - $stockInches;

        // ✅ Take absolute value if negative
        if ($remainingInches < 0) {
            $remainingInches = abs($remainingInches);
        }

        $ft = floor($remainingInches / $inchi);
        $inch = $remainingInches % $inchi;

        return $ft . '.' . str_pad($inch, 2, '0', STR_PAD_LEFT);
    }
    public function calculateFtAndInchiInIncreaseProductStockQty($qty_big, $qty_small)
    {
        // Parse big qty
        $b = explode('.', (string) $qty_big);
        $bFt = (int) $b[0];
        $bInch = isset($b[1]) ? (int) str_pad($b[1], 2, '0', STR_PAD_LEFT) : 0;

        // Parse small qty
        $s = explode('.', (string) $qty_small);
        $sFt = (int) $s[0];
        $sInch = isset($s[1]) ? (int) str_pad($s[1], 2, '0', STR_PAD_LEFT) : 0;

        // Convert both to total inches
        $bigInches = $bFt * 12 + $bInch;
        $smallInches = $sFt * 12 + $sInch;

        // Add total inches
        $totalInches = $bigInches + $smallInches;

        // Convert back to feet + inches
        $ft = floor($totalInches / 12);
        $inch = $totalInches % 12;

        // Return formatted value
        return $ft . '.' . str_pad($inch, 2, '0', STR_PAD_LEFT);
    }

}
