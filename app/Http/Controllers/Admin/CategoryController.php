<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\File;
use App\Handlers\Make;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\ItemType;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{

    public function get(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');
        $sortColumn = $request->input('sort_column', 'created_at');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', 5);

        $query = Category::query()->with('item_type', function ($query) {
            $query->select('id', 'name');
        });

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                // search by name, slug
                $q->where('name', 'like', "%{$search}%");
                $q->orWhere('slug', 'like', "%{$search}%");
            });
        });

        $query->when($filter, function ($query) use ($filter) {
            $query->where('status', $filter);
        });

        $query->when($sortColumn && $sort, function ($query) use ($sortColumn, $sort) {
            $query->orderBy($sortColumn, $sort);
        });

        // return $query->orderBy('created_at', 'desc')->paginate($perPage);
        if (!$search && !$filter && !$sortColumn && !$sort)
            $datas = $query->orderBy('created_at', 'desc')->paginate($perPage);
        else
            $datas = $query->paginate($perPage);

        return $datas;
    }
    public function index(): View
    {
        $theadColumns = [
            ['name' => 'Serial No.', 'sortable' => false],
            ['name' => 'Image', 'sortable' => false],
            ['name' => 'Category', 'sortable' => true, 'sortable_col' => 'name'],
            ['name' => 'Item Type', 'sortable' => false],
            ['name' => 'Status', 'sortable' => false],
            ['name' => 'Actions', 'sortable' => false],
        ];

        $breadcrumbs = [
            ['name' => 'Categories', 'route' => null, 'icon' => null]
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
        return view('pages.admin.category.base', compact('theadColumns', 'breadcrumbs', 'filters', 'setting'));
    }
    public function create(): View
    {
        $item_types = ItemType::where('status', 'active')->latest()->get();
        $breadcrumbs = [
            ['name' => 'Categories', 'route' => 'admin.categories', 'icon' => null],
            ['name' => 'Create', 'route' => null, 'icon' => null]
        ];

        return view('pages.admin.category.create', compact('breadcrumbs', 'item_types'));
    }
    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $slug = Make::slug($request, Category::class, 'name', null);

        $category = new Category();
        $category->name = $validated['name'];
        $category->item_type_id = $validated['item_type_id'];
        $category->slug = $slug;
        $category->status = $validated['status'];

        if ($image = File::store($request, 'image', 'categories', null)) {
            $category->image = $image;
        }

        $category->save();

        return redirect()
            ->route('admin.categories')
            ->with([
                'status' => 'success',
                'msg' => 'Category created!'
            ]);
    }
    public function edit($id): View
    {
        $item_types = ItemType::where('status', 'active')->latest()->get();
        $data = Category::findOrFail($id);
        $breadcrumbs = [
            ['name' => 'Brands', 'route' => 'admin.brands', 'icon' => null],
            ['name' => 'Edit', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.category.edit', compact('data', 'breadcrumbs', 'item_types'));
    }
    public function update(CategoryUpdateRequest $request, $id)
    {
        $validated = $request->validated();
        $category = Category::findOrFail(intval($id));

        $slug = Make::slug($request, Category::class, 'name', $category);
        $category->name = $validated['name'];
        $category->slug = $slug;
        $category->item_type_id = $validated['item_type_id'];
        $category->status = $validated['status'];
        if ($image = File::store($request, 'image', 'categories', $category)) {
            $category->image = $image;
        }
        $category->update();

        return redirect()
            ->route('admin.categories')
            ->with([
                'status' => 'success',
                'msg' => 'Category updated!'
            ]);
    }
    public function destroy($id)
    {
        $category = Category::findOrFail(intval($id));
        if ($category->image)
            File::delete($category, 'image');

        $category->delete();
        return response()->json([
            'status' => 'success',
            'msg' => 'Category deleted!'
        ]);
    }
}
