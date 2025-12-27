<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\File;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaffUpdateRequest;
use App\Models\Setting;
use App\Models\User;
use App\Http\Requests\Admin\StoreStaffRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaffController extends Controller
{
    public function get(Request $request)
    {
        // Get query parameters with defaults
        $search = $request->input('search', '');
        $filter = $request->input('filter', '');
        $sortColumn = $request->input('sort_column', 'created_at');
        $sort = $request->input('sort', 'desc');
        $perPage = $request->input('per_page', 5);

        $query = User::query()->whereNot('role', 'admin');

        $query->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        });

        $query->when($filter, function ($query) use ($filter) {
            $f = $filter === 'active' ? 1 : 0;
            $query->where('is_active', $f);
        });

        $query->when($sortColumn && $sort, function ($query) use ($sortColumn, $sort) {
            $query->orderBy($sortColumn, $sort);
        });

        if (!$search && !$filter && !$sortColumn && !$sort)
            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        else
            return $query->paginate($perPage);
    }
    public function index()
    {
        $theadColumns = [
            ['name' => 'Serial No.', 'sortable' => false],
            ['name' => 'Avatar', 'sortable' => false],
            ['name' => 'Username', 'sortable' => true, 'sortable_col' => 'name'],
            ['name' => 'Email', 'sortable' => true, 'sortable_col' => 'email'],
            ['name' => 'Phone Number', 'sortable' => true, 'sortable_col' => 'phone_number'],
            ['name' => 'Role', 'sortable' => false],
            // ['name' => 'Online Status', 'sortable' => false],
            ['name' => 'Account Status', 'sortable' => false],
            ['name' => 'Actions', 'sortable' => false],
        ];

        $breadcrumbs = [
            ['name' => 'User', 'route' => null, 'icon' => null]
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

        return view('pages.admin.staff.base', compact('theadColumns', 'breadcrumbs', 'filters', 'setting'));
    }
    public function show($id)
    {
        $staff = User::findOrFail(intval($id));
        $breadcrumbs = [
            ['name' => 'User', 'route' => 'admin.user', 'icon' => null],
            ['name' => 'Information', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.staff.show', compact('staff', 'breadcrumbs'));
    }
    public function create()
    {
        $breadcrumbs = [
            ['name' => 'User', 'route' => 'admin.user', 'icon' => null],
            ['name' => 'Create', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.staff.create', compact('breadcrumbs'));
    }
    public function store(StoreStaffRequest $request)
    {
        $validated = $request->validated();

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'salary' => $validated['salary'],
            'role' => $validated['role'],
            'is_active' => false,
            'password' => bcrypt(Str::random(12)), // Generate a random password
        ]);

        if ($avatar = File::store($request, 'avatar', 'avatars', $user))
            $user->update(['avatar' => $avatar]);

        // Create user profile
        $user->profile()->create([
            'city' => $validated['city'] ?? null,
            'zip_code' => $validated['zip_code'],
            'present_address' => $validated['present_address'],
            'address' => $validated['address'] ?? null,
        ]);

        return redirect()->route('admin.user')
            ->with([
                'msg' => 'Staff Added!',
                'status' => 'success',
            ]);
    }
    public function edit($id)
    {
        $staff = User::findOrFail(intval($id));
        $breadcrumbs = [
            ['name' => 'User', 'route' => 'admin.user', 'icon' => null],
            ['name' => 'Edit', 'route' => null, 'icon' => null]
        ];
        return view('pages.admin.staff.edit', compact('staff', 'breadcrumbs'));
    }
    public function update(StaffUpdateRequest $request, $id)
    {
        $validated = $request->validated();
        $user = User::findOrFail($id);

        // Update user data
        $user->name = $request->name;
        if ($request->email)
            $user->email = $request->email;
        if ($request->phone_number)
            $user->phone_number = $request->phone_number;

        $user->salary = $request->salary;
        $user->role = $request->role;
        $user->is_active = (bool) $request->is_active;
        if ($avatar = File::store($request, 'avatar', 'avatars', $user))
            $user->avatar = $avatar;
        $user->save();

        // Update or create profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'city' => $validated['city'] ?? null,
                'zip_code' => $validated['zip_code'],
                'present_address' => $validated['present_address'],
                'address' => $validated['address'] ?? null,
            ]
        );

        return redirect()->route('admin.user')
            ->with([
                'msg' => 'Staff Updated!',
                'status' => 'success',
            ]);
    }
    public function destroy($id)
    {
        $staff = User::find($id);

        if (!$staff) {
            return response()->json([
                'msg' => 'Staff not found!',
                'status' => 'warning',
            ]);
        }

        // Delete avatar if exists
        if ($staff->avatar)
            File::delete($staff, 'avatar');

        // Delete profile and associated card images if profile exists
        if ($staff->profile) {
            if ($staff->profile->card_front_side)
                File::delete($staff?->profile, 'card_front_side');
            if ($staff->profile->card_back_side)
                File::delete($staff?->profile, 'card_back_side');
            $staff->profile->delete();
        }

        // Delete the staff user
        $staff->delete();

        return response()->json([
            'status' => 'success',
            'msg' => 'Staff Deleted!',
        ]);
    }
}
