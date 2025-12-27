<nav id="sidebar" class="sidebar js-sidebar">
    <button type="button" class="btn sidebar-close-btn px-2">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="{{ route('welcome') }}">
        <span class="align-middle">
            <x-admin.logo />
        </span>
        </a>

        <ul class="sidebar-nav" style="padding-bottom: 5rem !important;">
            <li class="sidebar-header">Pages</li>

            <li class="sidebar-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.dashboard') }}"><i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span></a>
            </li>

            @if(in_array(auth()->user()->role, ['owner', 'admin', 'super_admin']))
            <li class="sidebar-item {{ Route::is('admin.user') || Route::is('admin.user.create') || Route::is('admin.user.show') || Route::is('admin.user.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.user') }}">
                    <i class="align-middle" data-feather="users"></i>
                    <span class="align-middle">User</span></a>
            </li>
            @endif

            <li class="sidebar-header">Inventory</li>

            <li class="sidebar-item {{ Route::is('admin.item.types') || Route::is('admin.item.type.create') || Route::is('admin.item.type.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.item.types') }}">
                    <i class="align-middle" data-feather="align-left"></i>
                    <span class="align-middle">Item Type</span></a>
            </li>

            <li class="sidebar-item {{ Route::is('admin.brands') || Route::is('admin.brand.create') || Route::is('admin.brand.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.brands') }}">
                    <i class="fa-solid fa-code-branch"></i>
                    <span class="align-middle">Brand</span>
                </a>
            </li>

            <li class="sidebar-item {{ Route::is('admin.categories') || Route::is('admin.category.create') || Route::is('admin.category.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.categories') }}">
                    <i class="fa-solid fa-list"></i>
                    <span class="align-middle">Category</span>
                </a>
            </li>

            <li class="sidebar-item {{ Route::is('admin.sub.categories') || Route::is('admin.sub.category.create') || Route::is('admin.sub.category.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.sub.categories') }}">
                    <i class="fa-solid fa-layer-group"></i>
                    <span class="align-middle">Sub Category</span></a>
            </li>

            <li class="sidebar-item {{ Route::is('admin.products') || Route::is('admin.product.create') || Route::is('admin.product.edit') || Route::is('admin.product.show') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.products') }}">
                    <i class="fa-solid fa-box"></i>
                    <span class="align-middle">Products</span></a>
            </li>

            <li class="sidebar-header">Sales</li>

            <li class="sidebar-item {{ Route::is('admin.customers') || Route::is('admin.customer.create') || Route::is('admin.customer.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.customers') }}">
                    <i class="fa-solid fa-users"></i>
                    <span class="align-middle">Customers</span></a>
            </li>

            <li class="sidebar-item {{ Route::is('admin.sales.orders') || Route::is('admin.sales.order.invoice') || Route::is('admin.sales.order.create') || Route::is('admin.sales.order.edit') ? 'active' : '' }}">
                <a class="sidebar-link" href="{{ route('admin.sales.orders') }}">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="align-middle">Sales Orders</span></a>
            </li>

            <li class="sidebar-header">Reports</li>

            <li class="sidebar-item {{ Route::is('admin.reports') ? 'active' : '' }}">
                <a href="{{route('admin.reports')}}" class="sidebar-link">
                    <i class="align-middle" data-feather="bar-chart-2"></i>
                    <span class="align-middle">All Reports</span>
                </a>
            </li>
            @if(in_array(auth()->user()->role, ['owner', 'admin', 'super_admin']))
            <li class="sidebar-header">Website Manage</li>
            <li class="sidebar-item {{ Route::is('admin.printing.contents') ? 'active' : '' }}">
                <a href="{{route('admin.printing.contents')}}" class="sidebar-link">
                    <i class="fa-solid fa-text-width"></i>
                    <span class="align-middle">Printing Content</span>
                </a>
            </li>
            <li class="sidebar-item {{ Route::is('admin.landing.page') ? 'active' : '' }}">
                <a href="{{route('admin.landing.page')}}" class="sidebar-link">
                    <i class="fa-solid fa-pager"></i>
                    <span class="align-middle">Landing Page</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
</nav>
<div class="sidebar-custom-overlay"></div>
