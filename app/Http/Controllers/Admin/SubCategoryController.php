<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\File;
use App\Handlers\Make;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubCategoryStoreRequest;
use App\Http\Requests\Admin\SubCategoryUpdateRequest;
use App\Models\Category;
use App\Models\Setting;
use App\Models\SubCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubCategoryController extends Controller
{
    public function get(Request $request)
    {
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');
        $sortColumn = $request->input('sort_column', 'created_at');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', 5);

        $query = SubCategory::query()->with('category', function ($query) {
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
            ['name' => 'Sub Category', 'sortable' => true, 'sortable_col' => 'name'],
            ['name' => 'Category', 'sortable' => false],
            ['name' => 'Status', 'sortable' => false],
            ['name' => 'Actions', 'sortable' => false],
        ];

        $breadcrumbs = [
            ['name' => 'Sub Categories', 'route' => null, 'icon' => null]
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
        return view('pages.admin.sub-category.base', compact('theadColumns', 'breadcrumbs', 'filters', 'setting'));
    }
    public function create(): View
    {
        $categories = Category::where('status', 'active')->latest()->get();
        $breadcrumbs = [
            ['name' => 'Sub Categories', 'route' => 'admin.sub.categories', 'icon' => null],
            ['name' => 'Create', 'route' => null, 'icon' => null]
        ];

        return view('pages.admin.sub-category.create', compact('breadcrumbs', 'categories'));
    }
    public function store(SubCategoryStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $slug = Make::slug($request, SubCategory::class, 'name', null);

        $sub_category = new SubCategory();
        $sub_category->name = $validated['name'];
        $sub_category->category_id = $validated['category_id'];
        $sub_category->slug = $slug;
        $sub_category->status = $validated['status'];

        if ($image = File::store($request, 'image', 'sub_categories', null)) {
            $sub_category->image = $image;
        }

        $sub_category->save();

        return redirect()
            ->route('admin.sub.categories')
            ->with([
                'status' => 'success',
                'msg' => 'Sub Category created!'
            ]);
    }
    public function edit($id): View
    {
        $categories = Category::where('status', 'active')->latest()->get();
        $data = SubCategory::findOrFail($id);
        $breadcrumbs = [
            ['name' => 'Sub Categories', 'route' => 'admin.sub.categories', 'icon' => null],
            ['name' => 'Edit', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.sub-category.edit', compact('data', 'breadcrumbs', 'categories'));
    }
    public function update(SubCategoryUpdateRequest $request, $id)
    {
        $validated = $request->validated();
        $sub_category = SubCategory::findOrFail(intval($id));

        $slug = Make::slug($request, SubCategory::class, 'name', $sub_category);
        $sub_category->name = $validated['name'];
        $sub_category->slug = $slug;
        $sub_category->category_id = $validated['category_id'];
        $sub_category->status = $validated['status'];
        if ($image = File::store($request, 'image', 'sub_categories', $sub_category)) {
            $sub_category->image = $image;
        }
        $sub_category->update();

        return redirect()
            ->route('admin.sub.categories')
            ->with([
                'status' => 'success',
                'msg' => 'Sub Category updated!'
            ]);
    }
    public function destroy($id)
    {
        $sub_category = SubCategory::findOrFail(intval($id));
        if ($sub_category->image)
            File::delete($sub_category, 'image');

        $sub_category->delete();
        return response()->json([
            'status' => 'success',
            'msg' => 'Sub Category deleted!'
        ]);
    }
}
