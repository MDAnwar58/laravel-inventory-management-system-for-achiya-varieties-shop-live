<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\Make;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ItemTypeStoreRequest;
use App\Http\Requests\Admin\ItemTypeUpdateRequest;
use App\Models\ItemType;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ItemTypeController extends Controller
{
    public function get(Request $request)
    {
        // Get query parameters with defaults
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');
        $sortColumn = $request->input('sort_column', 'created_at');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', 5);

        $query = ItemType::query()->select('id', 'name', 'slug', 'status');

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
            ['name' => 'Item Type', 'sortable' => true, 'sortable_col' => 'name'],
            ['name' => 'Status', 'sortable' => false],
            ['name' => 'Actions', 'sortable' => false],
        ];

        $breadcrumbs = [
            ['name' => 'Item Types', 'route' => null, 'icon' => null]
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
        return view('pages.admin.item-types.base', compact('theadColumns', 'breadcrumbs', 'filters', 'setting'));
    }
    public function create(): View
    {
        $breadcrumbs = [
            ['name' => 'Item Types', 'route' => 'admin.item.types', 'icon' => null],
            ['name' => 'Create', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.item-types.create', compact('breadcrumbs'));
    }
    public function store(ItemTypeStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $slug = Make::slug($request, ItemType::class, 'name', null);

        $itemType = new ItemType();
        $itemType->name = $validated['name'];
        $itemType->slug = $slug;
        $itemType->status = $validated['status'];
        $itemType->save();

        return redirect()
            ->route('admin.item.types')
            ->with([
                'status' => 'success',
                'msg' => 'Item type created!'
            ]);
    }
    public function edit($id): View
    {
        $data = ItemType::findOrFail($id);
        $breadcrumbs = [
            ['name' => 'Item Types', 'route' => 'admin.item.types', 'icon' => null],
            ['name' => 'Edit', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.item-types.edit', compact('data', 'breadcrumbs'));
    }
    public function update(ItemTypeUpdateRequest $request, $id)
    {
        $validated = $request->validated();
        $itemType = ItemType::findOrFail(intval($id));

        $slug = Make::slug($request, ItemType::class, 'name', $itemType);
        $itemType->name = $validated['name'];
        $itemType->slug = $slug;
        $itemType->status = $validated['status'];
        $itemType->update();

        return redirect()
            ->route('admin.item.types')
            ->with([
                'status' => 'success',
                'msg' => 'Item type updated!'
            ]);
    }

    /**
     * Remove the specified item type from storage.
     */
    public function destroy($id)
    {
        $itemType = ItemType::findOrFail(intval($id));
        $itemType->delete();
        return response()->json([
            'status' => 'success',
            'msg' => 'Item type deleted!'
        ]);
    }
}
