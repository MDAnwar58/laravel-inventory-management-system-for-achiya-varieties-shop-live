<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CustomerStoreRequest;
use App\Http\Requests\Admin\CustomerUpdateRequest;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerController extends Controller
{
    public function get(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');
        $sortColumn = $request->input('sort_column', 'created_at');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', 5);
        $id = $request->input('id', '');

        $query = Customer::query()->select('id', 'name', 'phone', 'address', 'created_at');
        if (!$id) {
            $query->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                    $q->orWhere('email', 'like', "%{$search}%");
                    $q->orWhere('phone', 'like', "%{$search}%");
                });
            });

            $query->when($filter, function ($query) use ($filter) {
                $query->where('status', $filter);
            });

            $query->when($sortColumn && $sort, function ($query) use ($sortColumn, $sort) {
                $query->orderBy($sortColumn, $sort);
            });

            if (!$search && !$filter && !$sortColumn && !$sort)
                $datas = $query->orderBy('created_at', 'desc')->paginate($perPage);
            else
                $datas = $query->paginate($perPage);
        } else {
            // Base query
            $baseQuery = clone $query;

            // 1️⃣ Get the ID data (always included)
            $idData = (clone $baseQuery)
                ->when($id, function ($q) use ($id) {
                    $q->where('id', $id);
                })
                ->get();

            // 2️⃣ Get search data outside this ID
            $searchData = [];
            if (!empty($search)) {
                $searchData = (clone $baseQuery)
                    ->when($search, function ($q) use ($search) {
                        $q->where(function ($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                    })
                    ->when($id, function ($q) use ($id) {
                        $q->where('id', '!=', $id);
                    })
                    ->get();
            }

            // 3️⃣ Merge both
            $combined = $idData->merge($searchData)->unique('id')->values();

            // 4️⃣ Paginate manually
            $page = LengthAwarePaginator::resolveCurrentPage();
            $total = $combined->count();
            $items = $combined->slice(($page - 1) * $perPage, $perPage)->values();

            $paginated = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath()]
            );

            $datas = $paginated;
        }

        return $datas;
    }
    public function index(): View
    {
        $theadColumns = [
            ['name' => 'Serial No.', 'sortable' => false],
            ['name' => 'Customer', 'sortable' => true, 'sortable_col' => 'name'],
            ['name' => 'Phone', 'sortable' => true, 'sortable_col' => 'phone'],
            ['name' => 'Address', 'sortable' => true, 'sortable_col' => 'address'],
            ['name' => 'Actions', 'sortable' => false],
        ];

        $breadcrumbs = [
            ['name' => 'Customers', 'route' => null, 'icon' => null]
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
        return view('pages.admin.customer.base', compact('theadColumns', 'breadcrumbs', 'filters', 'setting'));
    }
    public function create(): View
    {
        $breadcrumbs = [
            ['name' => 'Customers', 'route' => 'admin.customers', 'icon' => null],
            ['name' => 'Create', 'route' => null, 'icon' => null]
        ];

        return view('pages.admin.customer.create', compact('breadcrumbs'));
    }
    public function store(CustomerStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $customer = new Customer();
        $customer->name = $validated['name'];
        $customer->phone = $validated['phone'];
        $customer->address = $validated['address'];
        $customer->save();

        return redirect()
            ->route('admin.customers')
            ->with([
                'status' => 'success',
                'msg' => 'Customer created!'
            ]);
    }
    public function edit($id): View
    {
        $data = Customer::findOrFail($id);
        $breadcrumbs = [
            ['name' => 'Customers', 'route' => 'admin.customers', 'icon' => null],
            ['name' => 'Edit', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.customer.edit', compact('data', 'breadcrumbs'));
    }
    public function update(CustomerUpdateRequest $request, $id)
    {
        $validated = $request->validated();
        $customer = Customer::findOrFail(intval($id));

        $customer->name = $validated['name'];
        if (array_key_exists('phone', $validated) && $validated['phone'])
            $customer->phone = $validated['phone'];
        else
            $customer->phone = null;
        $customer->address = $validated['address'];
        $customer->update();

        return redirect()
            ->route('admin.customers')
            ->with([
                'status' => 'success',
                'msg' => 'Customer updated!'
            ]);
    }
    public function destroy($id)
    {
        $customer = Customer::findOrFail(intval($id));
        if (!$customer) {

            return response()->json([
                'status' => 'warning',
                'msg' => 'Customer not found!'
            ]);
        }
        $customer->delete();
        return response()->json([
            'status' => 'success',
            'msg' => 'Customer deleted!'
        ]);
    }
}
