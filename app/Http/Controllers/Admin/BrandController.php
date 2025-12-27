<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\File;
use App\Handlers\Make;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandStoreRequest;
use App\Http\Requests\Admin\BrandUpdateRequest;
use App\Models\Brand;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function get(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');
        $sortColumn = $request->input('sort_column', 'created_at');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', 5);

        $query = Brand::query()->select('id', 'name', 'slug', 'status', 'image');

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });

        $query->when($filter, function ($query) use ($filter) {
            $query->where('status', $filter);
        });

        $query->when($sortColumn && $sort, function ($query) use ($sortColumn, $sort) {
            $query->orderBy($sortColumn, $sort);
        });

        if (!$search && !$filter && !$sortColumn && !$sort)
            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        else
            return $query->paginate($perPage);
    }
    public function index(): View
    {
        $theadColumns = [
            ['name' => 'Serial No.', 'sortable' => false],
            ['name' => 'Image', 'sortable' => false],
            ['name' => 'Brand', 'sortable' => true, 'sortable_col' => 'name'],
            ['name' => 'Status', 'sortable' => false],
            ['name' => 'Actions', 'sortable' => false],
        ];

        $breadcrumbs = [
            ['name' => 'Brands', 'route' => null, 'icon' => null]
        ];

        $filters = [
            [
                ['name' => 'Filter Status', 'value' => '', 'disabled' => true],
                ['name' => 'All', 'value' => ''],
                ['name' => 'Active', 'value' => 'active'],
                ['name' => 'Deactive', 'value' => 'deactive'],
            ],
        ];
        $setting = Setting::first();
        return view('pages.admin.brand.base', compact('theadColumns', 'breadcrumbs', 'filters', 'setting'));
    }
    public function create(): View
    {
        $breadcrumbs = [
            ['name' => 'Brands', 'route' => 'admin.brands', 'icon' => null],
            ['name' => 'Create', 'route' => null, 'icon' => null]
        ];

        return view('pages.admin.brand.create', compact('breadcrumbs'));
    }
    public function store(BrandStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $slug = Make::slug($request, Brand::class, 'name', null);

        $brand = new Brand();
        $brand->name = $validated['name'];
        $brand->slug = $slug;
        $brand->status = $validated['status'];

        if ($image = File::store($request, 'image', 'brands', null)) {
            $brand->image = $image;
        }

        $brand->save();

        return redirect()
            ->route('admin.brands')
            ->with([
                'status' => 'success',
                'msg' => 'Brand created!'
            ]);
    }
    public function edit($id): View
    {
        $data = Brand::findOrFail($id);
        $breadcrumbs = [
            ['name' => 'Brands', 'route' => 'admin.brands', 'icon' => null],
            ['name' => 'Edit', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.brand.edit', compact('data', 'breadcrumbs'));
    }
    public function update(BrandUpdateRequest $request, $id)
    {
        $validated = $request->validated();
        $brand = Brand::findOrFail(intval($id));

        $slug = Make::slug($request, Brand::class, 'name', $brand);
        $brand->name = $validated['name'];
        $brand->slug = $slug;
        $brand->status = $validated['status'];
        if ($image = File::store($request, 'image', 'brands', $brand)) {
            $brand->image = $image;
        }
        $brand->update();

        return redirect()
            ->route('admin.brands')
            ->with([
                'status' => 'success',
                'msg' => 'Brand updated!'
            ]);
    }
    public function destroy($id)
    {
        $brand = Brand::findOrFail(intval($id));
        if ($brand->image) {
            File::delete($brand, 'image');
        }
        $brand->delete();
        return response()->json([
            'status' => 'success',
            'msg' => 'Brand deleted!'
        ]);
    }
}
