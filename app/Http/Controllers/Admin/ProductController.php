<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\Data;
use App\Handlers\File;
use App\Handlers\Make;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductStoreRequest;
use App\Http\Requests\Admin\ProductUpdateRequest;
use App\Http\Requests\Admin\SubCategoryStoreRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ItemType;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SubCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductController extends Controller
{

    public function get(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');
        $stock_filter = $request->input('stock_filter', '');
        $item_type_filter = $request->input('item_type_filter', '');
        $brand_filter = $request->input('brand_filter', '');
        $category_filter = $request->input('category_filter', '');
        $sub_category_filter = $request->input('sub_category_filter', '');
        $sortColumn = $request->input('sort_column', 'created_at');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', 5);
        // $ids = $request->input('ids', '');
        if (Cache::get('low_stock_alert') && $stock_filter) {
            Cache::forget('low_stock_alert');
            Cache::forget('low_stock_products_list');
        }


        $query = Product::query()->with([
            'item_type' => function ($q) {
                $q->select('id', 'name');
            },
            'brand' => function ($q) {
                $q->select('id', 'name');
            },
            'category' => function ($q) {
                $q->select('id', 'name');
            },
            'sub_category' => function ($q) {
                $q->select('id', 'name');
            },
        ]);

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                // search by name, slug, sku
                $q->where('name', 'like', "%{$search}%");
                $q->orWhere('slug', 'like', "%{$search}%");
                $q->orWhere('sku', 'like', "%{$search}%");
            });
        });

        $query->when($filter, function ($query) use ($filter) {
            $query->where('status', $filter);
        });

        $query->when($stock_filter, function ($query) use ($stock_filter) {
            if ($stock_filter === "low stock")
                $query->whereColumn('stock', '<=', 'low_stock_level');
            else
                $query->whereColumn('stock', '>', 'low_stock_level');
        });

        $query->when($item_type_filter, function ($query) use ($item_type_filter) {
            $item_type_id = (int) $item_type_filter;
            $query->where('item_type_id', $item_type_id);
        });

        $query->when($brand_filter, function ($query) use ($brand_filter) {
            $brand_id = (int) $brand_filter;
            $query->where('brand_id', $brand_id);
        });

        $query->when($category_filter, function ($query) use ($category_filter) {
            $category_id = (int) $category_filter;
            $query->where('category_id', $category_id);
        });

        $query->when($sub_category_filter, function ($query) use ($sub_category_filter) {
            $sub_category_id = (int) $sub_category_filter;
            $query->where('sub_category_id', $sub_category_id);
        });

        $query->when($sortColumn && $sort, function ($query) use ($sortColumn, $sort) {
            $query->orderBy($sortColumn, $sort);
        });

        // return $query->orderBy('created_at', 'desc')->paginate($perPage);
        if (!$sortColumn && !$sort)
            $datas = $query->orderBy('created_at', 'desc')->paginate($perPage);
        else
            $datas = $query->paginate($perPage);

        return $datas;
    }
    // public function test()
    // {
    //     $pIds = $this->string_to_int_array($ids);
    //     // Base query
    //     $baseQuery = clone $query;

    //     // 1️⃣ Get IDs data (always included)
    //     $idsData = (clone $baseQuery)
    //         ->when(!empty($pIds), function ($q) use ($pIds) {
    //             $q->whereIn('id', $pIds);
    //         })
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     // 2️⃣ Get search data outside IDs
    //     $searchData = [];
    //     if (!empty($search)) {
    //         $searchData = (clone $baseQuery)
    //             ->when($search, function ($q) use ($search) {
    //                 $q->where(function ($q) use ($search) {
    //                     $q->where('name', 'like', "%{$search}%")
    //                         ->orWhere('slug', 'like', "%{$search}%")
    //                         ->orWhere('sku', 'like', "%{$search}%");
    //                 });
    //             })
    //             ->when(!empty($pIds), function ($q) use ($pIds) {
    //                 $q->whereNotIn('id', $pIds);
    //             })
    //             ->get();
    //     }

    //     // 3️⃣ Merge both
    //     $combined = $idsData->merge($searchData)->unique('id')->values();

    //     // 4️⃣ Paginate manually
    //     $page = LengthAwarePaginator::resolveCurrentPage();
    //     $total = $combined->count();
    //     $items = $combined->slice(($page - 1) * $perPage, $perPage)->values();

    //     $paginated = new LengthAwarePaginator(
    //         $items,
    //         $total,
    //         $perPage,
    //         $page,
    //         ['path' => LengthAwarePaginator::resolveCurrentPath()]
    //     );

    //     $datas = $paginated;
    // }
    public function get_for_sales_order(Request $request)
    {
        $search = $request->input('search', '');
        $item_type_filter = $request->input('item_type_filter', '');
        $brand_filter = $request->input('brand_filter', '');
        $category_filter = $request->input('category_filter', '');
        $sub_category_filter = $request->input('sub_category_filter', '');
        $perPage = $request->input('per_page', 5);
        $ids = $request->input('ids', '');
        $ids_with_other = $request->input('ids_with_others', false);
        // echo $ids_with_other . "<br>" . $ids;
        // Base query
        $query = Product::query()->with([
            'item_type:id,name',
            'brand:id,name',
            'category:id,name',
            'sub_category:id,name',
        ]);

        // Convert IDs to array
        if (empty($ids) || $ids === '[]')
            $idArray = [];
        else
            $idArray = $this->string_to_int_array($ids);

        // 1️⃣ If IDs are passed, fetch them first (only on first page)
        $idsData = collect();

        if (count($idArray) > 0 && LengthAwarePaginator::resolveCurrentPage() > 0) {
            $idsData = (clone $query)
                ->whereIn('id', $idArray)
                ->orderByRaw('FIELD(id, ' . implode(',', $idArray) . ')'); // preserve order
            if ($ids_with_other === 'false') {
                $idsData = $idsData->orderBy('created_at', 'desc')->get();
                return Data::paginate($idsData, $perPage);
            } else {
                $idsData = $idsData->get();
            }
        }

        // 2️⃣ Build main filtered query
        $mainQuery = (clone $query)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($item_type_filter, fn($q) => $q->where('item_type_id', (int) $item_type_filter))
            ->when($brand_filter, fn($q) => $q->where('brand_id', (int) $brand_filter))
            ->when($category_filter, fn($q) => $q->where('category_id', (int) $category_filter))
            ->when($sub_category_filter, fn($q) => $q->where('sub_category_id', (int) $sub_category_filter))
            ->when(!empty($idArray), fn($q) => $q->whereNotIn('id', $idArray)) // prevent duplicates
            ->orderBy('created_at', 'desc');

        $filteredData = $mainQuery->get();

        // 3️⃣ Merge IDs first, then others
        $combined = $idsData->merge($filteredData)->unique('id')->values();

        // 4️⃣ Manual pagination
        $page = LengthAwarePaginator::resolveCurrentPage();
        $total = $combined->count();
        $items = $combined->slice(($page - 1) * $perPage, $perPage)->values();

        $datas = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return $datas;
    }
    // public function get_for_sales_order(Request $request)
    // {
    //     $search = $request->input('search', '');
    //     $item_type_filter = $request->input('item_type_filter', '');
    //     $brand_filter = $request->input('brand_filter', '');
    //     $category_filter = $request->input('category_filter', '');
    //     $sub_category_filter = $request->input('sub_category_filter', '');
    //     $perPage = $request->input('per_page', 5);
    //     $ids = $request->input('ids', '');

    //     // Base query
    //     $query = Product::query()->with([
    //         'item_type:id,name',
    //         'brand:id,name',
    //         'category:id,name',
    //         'sub_category:id,name',
    //     ]);

    //     // Convert IDs to array
    //     $idArray = $this->string_to_int_array($ids);

    //     // 1️⃣ If IDs are passed, fetch them first (only on first page)
    //     $idsData = collect();
    //     if (!empty($idArray) && LengthAwarePaginator::resolveCurrentPage() === 1) {
    //         $idsData = (clone $query)
    //             ->whereIn('id', $idArray)
    //             ->orderByRaw('FIELD(id, ' . implode(',', $idArray) . ')') // preserve order
    //             ->get();
    //     }

    //     // 2️⃣ Build main filtered query
    //     $mainQuery = (clone $query)
    //         ->when($search, function ($q) use ($search) {
    //             $q->where(function ($subQ) use ($search) {
    //                 $subQ->where('name', 'like', "%{$search}%")
    //                     ->orWhere('slug', 'like', "%{$search}%")
    //                     ->orWhere('sku', 'like', "%{$search}%");
    //             });
    //         })
    //         ->when($item_type_filter, fn($q) => $q->where('item_type_id', (int) $item_type_filter))
    //         ->when($brand_filter, fn($q) => $q->where('brand_id', (int) $brand_filter))
    //         ->when($category_filter, fn($q) => $q->where('category_id', (int) $category_filter))
    //         ->when($sub_category_filter, fn($q) => $q->where('sub_category_id', (int) $sub_category_filter))
    //         ->when(!empty($idArray), fn($q) => $q->whereNotIn('id', $idArray)) // prevent duplicates
    //         ->orderBy('created_at', 'desc');

    //     $filteredData = $mainQuery->get();

    //     // 3️⃣ Merge IDs first, then others
    //     $combined = $idsData->merge($filteredData)->unique('id')->values();

    //     // 4️⃣ Manual pagination
    //     $page = LengthAwarePaginator::resolveCurrentPage();
    //     $total = $combined->count();
    //     $items = $combined->slice(($page - 1) * $perPage, $perPage)->values();

    //     $datas = new LengthAwarePaginator(
    //         $items,
    //         $total,
    //         $perPage,
    //         $page,
    //         ['path' => LengthAwarePaginator::resolveCurrentPath()]
    //     );

    //     return $datas;
    // }

    public function index(): View
    {
        $theadColumns = [
            ['name' => 'Product SKU', 'sortable' => true, 'sortable_col' => 'sku'],
            ['name' => 'Image', 'sortable' => false],
            ['name' => 'Product', 'sortable' => true, 'sortable_col' => 'name'],
            ['name' => 'Item Type', 'sortable' => false],
            ['name' => 'Brand', 'sortable' => false],
            ['name' => 'Category', 'sortable' => false],
            ['name' => 'Sub Category', 'sortable' => false],
            ['name' => 'Wholesale Price', 'sortable' => true, 'sortable_col' => 'price'],
            // ['name' => 'Discount Price', 'sortable' => true, 'sortable_col' => 'discount_price'],
            ['name' => 'Retail Price', 'sortable' => true, 'sortable_col' => 'retail_price'],
            ['name' => 'Cost Price', 'sortable' => true, 'sortable_col' => 'cost_price'],
            ['name' => 'Purchase Limit', 'sortable' => false],
            ['name' => 'Stock Weight/Foot/Yard/Meter', 'sortable' => true, 'sortable_col' => 'stock_w'],
            ['name' => 'Stock Pieces', 'sortable' => true, 'sortable_col' => 'stock'],
            ['name' => 'Stock Level', 'sortable' => true, 'sortable_col' => 'low_stock_level'],
            ['name' => 'Status', 'sortable' => false],
            ['name' => 'Actions', 'sortable' => false],
        ];

        $breadcrumbs = [
            ['name' => 'Products', 'route' => null, 'icon' => null]
        ];
        $filters = [
            'status' => [
                ['name' => 'Filter Status', 'value' => '', 'disabled' => true],
                ['name' => 'All', 'value' => ''],
                ['name' => 'Active', 'value' => 'active'],
                ['name' => 'Deactive', 'value' => 'deactive'],
            ],
        ];
        $setting = Setting::first();
        $item_types = ItemType::latest()->get();
        $brands = Brand::latest()->get();
        $categories = Category::latest()->get();
        $sub_categories = SubCategory::latest()->get();

        return view('pages.admin.product.base', compact('theadColumns', 'breadcrumbs', 'filters', 'setting', 'item_types', 'brands', 'categories', 'sub_categories'));
    }
    public function create(): View
    {
        $item_types = ItemType::where('status', 'active')->latest()->get();
        $brands = Brand::where('status', 'active')->latest()->get();
        $categories = Category::where('status', 'active')->latest()->get();
        $sub_categories = SubCategory::where('status', 'active')->latest()->get();
        $breadcrumbs = [
            ['name' => 'Products', 'route' => 'admin.products', 'icon' => null],
            ['name' => 'Create', 'route' => null, 'icon' => null]
        ];

        return view('pages.admin.product.create', compact('breadcrumbs', 'item_types', 'brands', 'categories', 'sub_categories'));
    }
    public function show($id)
    {
        $data = Product::with([
            'sales_orders_products' => function ($query) {
                return [
                    'sales_order' => $query->whereHas('salesOrder', function ($q) {
                        $q->where('status', '!=', 'cancelled');
                    }),
                ];
            }
        ])->findOrFail(intval($id));

        $total_solded = $this->get_total_solded($data);
        $total_wholesale_earnings = $this->get_total_wholesale_earnings($data);
        $total_retail_earnings = $this->get_total_retail_earnings($data);
        $total_profits = $this->get_total_profit($data);
        $stock = $data->stock_w_type === "none" ? $data->stock : $data->stock_w;
        $current_stock = $this->get_current_stock($data);

        $breadcrumbs = [
            ['name' => 'Products', 'route' => 'admin.products', 'icon' => null],
            ['name' => 'Show', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.product.show', compact('data', 'breadcrumbs', 'total_solded', 'total_wholesale_earnings', 'total_retail_earnings', 'total_profits', 'stock', 'current_stock'));
    }
    public function store(ProductStoreRequest $request)
    {
        $validated = $request->validated();
        $slug = Make::slug($request, Product::class, 'name', null);
        $sku = Make::sku();

        $product = new Product();
        $product->sku = $sku;
        // if ($validated['barcode'])
        //     $product->barcode = $validated['barcode'];

        $product->name = $validated['name'];
        $product->slug = $slug;

        $product->item_type_id = $validated['item_type_id'];
        $product->brand_id = $validated['brand_id'];
        $product->category_id = $validated['category_id'];
        $product->sub_category_id = $validated['sub_category_id'];

        $product->price = $validated['price'];
        if (isset($validated['discount_price']))
            $product->discount_price = $validated['discount_price'];
        $product->retail_price = $validated['retail_price'];
        $product->cost_price = $validated['cost_price'];

        $product->stock = $validated['stock_w_type'] === "none" ? $validated['stock'] : null;
        $product->stock_w = $validated['stock_w_type'] !== "none" ? $validated['stock_w'] : null;
        $product->stock_w_type = $validated['stock_w_type'];
        if (isset($validated['low_stock_level']))
            $product->low_stock_level = $validated['low_stock_level'];
        if (isset($validated['purchase_limit']))
            $product->purchase_limit = $validated['purchase_limit'];

        if ($validated['desc'])
            $product->desc = $validated['desc'];

        $product->status = $validated['status'];

        if ($image = File::store($request, 'image', 'products', null)) {
            $product->image = $image;
        }

        $product->save();

        return redirect()
            ->route('admin.products')
            ->with([
                'status' => 'success',
                'msg' => 'Product created!'
            ]);
    }
    public function edit($id): View
    {
        $item_types = ItemType::where('status', 'active')->latest()->get();
        $brands = Brand::where('status', 'active')->latest()->get();
        $categories = Category::where('status', 'active')->latest()->get();
        $sub_categories = SubCategory::where('status', 'active')->latest()->get();
        $data = Product::findOrFail(intval($id));
        $breadcrumbs = [
            ['name' => 'Products', 'route' => 'admin.products', 'icon' => null],
            ['name' => 'Edit', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.product.edit', compact('data', 'breadcrumbs', 'item_types', 'brands', 'categories', 'sub_categories'));
    }
    public function update(ProductUpdateRequest $request, $id)
    {
        $validated = $request->validated();
        $product = Product::findOrFail(intval($id));

        $slug = Make::slug($request, Product::class, 'name', $product);
        $currentDateTime = now()->format('Y-m-d H:i:s');

        // if ($validated['barcode'])
        //     $product->barcode = $validated['barcode'];

        $product->sku = Make::sku();
        $product->name = $validated['name'];
        $product->slug = $slug;
        $product->item_type_id = $validated['item_type_id'];
        $product->brand_id = $validated['brand_id'];
        $product->category_id = $validated['category_id'];
        $product->sub_category_id = $validated['sub_category_id'];

        // change price status pass in request
        if ((int) $validated['price'] !== (int) $product->price) {
            $old_price = $product->price; // from
            $new_price = $validated['price']; // to
            $change_price = 'Last price changed from ৳' . $old_price . ' to ৳' . $new_price;
            $product->change_price = $change_price;
            $product->change_price_updated_at = $currentDateTime;
        }
        $product->price = $validated['price'];
        if (isset($validated['discount_price']))
            $product->discount_price = $validated['discount_price'];
        $product->retail_price = $validated['retail_price'];
        $product->cost_price = $validated['cost_price'];

        // change stock status
        $req_stock = $validated['stock_w_type'] === "none" ? isset($validated['stock']) && $validated['stock'] : $validated['stock_w'];
        $old_stock = $validated['stock_w_type'] === "none" ? $product->stock : $product->stock_w;
        if ((int) $req_stock > (int) $old_stock) {
            $stock_update = (int) $req_stock - (int) $old_stock;
            $stock_updated = 'Last stock updated ' . $stock_update . ' units';
            $product->stock_updated = $stock_updated;
            $product->stock_updated_at = $currentDateTime;
        }
        $product->stock = $validated['stock_w_type'] === "none" ? $validated['stock'] : null;
        $product->stock_w = $validated['stock_w_type'] !== "none" ? $validated['stock_w'] : null;
        $product->stock_w_type = $validated['stock_w_type'];
        if (isset($validated['low_stock_level']))
            $product->low_stock_level = $validated['low_stock_level'];
        if (isset($validated['purchase_limit']))
            $product->purchase_limit = $validated['purchase_limit'];

        if ($validated['desc'])
            $product->desc = $validated['desc'];
        $product->status = $validated['status'];
        if ($image = File::store($request, 'image', 'products', $product)) {
            $product->image = $image;
        }
        $product->update();

        return redirect()
            ->route('admin.products')
            ->with([
                'status' => 'success',
                'msg' => 'Product updated!'
            ]);
    }
    public function destroy($id)
    {
        $product = Product::findOrFail(intval($id));
        if ($product->image)
            File::delete($product, 'image');

        $product->delete();

        return response()->json([
            'status' => 'success',
            'msg' => 'Product deleted!'
        ]);
    }
    public function delete($id)
    {
        $product = Product::findOrFail(intval($id));
        if ($product->image)
            File::delete($product, 'image');

        $product->delete();

        return redirect()->route('admin.products')
            ->with([
                'status' => 'success',
                'msg' => 'Product deleted!'
            ]);
    }

    // extra function
    public function get_total_solded($product)
    {
        $total_solded = 0;
        $weight_type = '';
        $stock_w_type = false;

        if ($product->sales_orders_products->count() > 0) {
            foreach ($product->sales_orders_products as $order_product) {
                $total_solded += (float) $order_product->qty;

                // Capture weight type from the product
                if ($order_product->stock_w_type !== 'none') {
                    $weight_type = $this->kg_or_gm_or_fit_or_yard_or_m_inchi($total_solded, $order_product->stock_w_type);
                    $stock_w_type = true;
                } else {
                    $weight_type = ' pcs';
                    $stock_w_type = false;
                }
            }

            // ✅ Format total only once after loop
            $total_solded = $stock_w_type ? $this->formatNumber($total_solded, $order_product->stock_w_type) : $total_solded;
        }

        return $total_solded . $weight_type;
    }
    public function get_total_wholesale_earnings($product)
    {
        return $product->sales_orders_products
            ->filter(fn($order_product) => (bool) $order_product->retail_price_status === false)
            ->sum('total');
    }
    public function get_total_retail_earnings($product)
    {
        return $product->sales_orders_products
            ->filter(fn($order_product) => (bool) $order_product->retail_price_status === true)
            ->sum('total');
    }
    public function get_total_earnings($product)
    {
        $total_solded = 0;
        if ($product->sales_orders_products->count() > 0) {
            foreach ($product->sales_orders_products as $order_product) {
                $total_solded += $order_product->qty;
            }
        }
        return $total_solded;
    }
    public function get_total_profit($product)
    {
        $total_price = 0;
        $total_cost_price = 0;
        if ($product->sales_orders_products->count() > 0) {
            foreach ($product->sales_orders_products as $order_product) {
                $total_price += $order_product->total;
                $total_cost_price = $product->cost_price * $order_product->qty;
            }
        }
        return $total_price - $total_cost_price;
    }
    public function get_current_stock($data)
    {
        return $data->stock_w_type === "none"
            ? $data->stock . ' pcs'
            : $this->formatNumber((float) $data->stock_w, $data->stock_w_type) . $this->kg_or_gm_or_fit_or_yard_or_m_inchi((float) $data->stock_w, $data->stock_w_type);
    }
    public function string_to_int_array($str)
    {
        $cleanStr = str_replace(['[', ']'], '', $str);
        $result = explode(',', $cleanStr);
        $int_array = array_map('intval', $result);
        return $int_array;
    }
    public function formatNumber($num, $type = 'none')
    {
        $n = (float) $num;
        if ($type === 'ft' || $type === 'yard' || $type === 'm') {
            if ($n < 1)
                return rtrim(rtrim(number_format($num * 100, 2, '.', ''), '0'), '.');

            return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
        } else {
            if ($n < 1)
                return preg_replace('/\.0+$/', '', (string) ($n * 1000));
            // strip trailing .000 etc. after 3-decimal rounding
            return rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
        }
    }
    public function kg_or_gm_or_fit_or_yard_or_m_inchi($num, $type = 'kg')
    {
        if ($type === 'kg') {
            $n = (float) $num;
            return $n < 1 ? 'gm' : 'kg';
        } else if ($type === 'ft') {
            $n = (float) $num;
            return $n < 1 ? 'inchi' : 'ft';
        } else if ($type === 'yard') {
            $n = (float) $num;
            return $n < 1 ? 'inchi' : 'yard';
        } else if ($type === 'm') {
            $n = (float) $num;
            return $n < 1 ? 'inchi' : 'm';
        }
    }
}
