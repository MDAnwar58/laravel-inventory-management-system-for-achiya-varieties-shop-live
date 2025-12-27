@props([
    'breadcrumbs' => [],
    'create_btn_name' => null,
    'btn_route' => null,
])
@if(!empty($breadcrumbs))
  <div class="d-flex justify-content-between align-items-start pb-1">
    <nav
      style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);"
      aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">
          <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-1 text-dark"><i
              class="align-middle" data-feather="sliders" style="width: 13px; height: 13px;"></i>
            <span>Dashboard</span></a>
        </li>
        @if(count($breadcrumbs) > 0)
          @foreach($breadcrumbs as $breadcrumb)
            @if($breadcrumb['route'])
              <li class="breadcrumb-item">
                <a href="{{ route($breadcrumb['route']) }}" class="d-flex align-items-center gap-1 text-dark">
                  @if($breadcrumb['icon'])
                    <i class="align-middle" data-feather="{{ $breadcrumb['icon'] }}" style="width: 13px; height: 13px;"></i>
                  @endif
                  <span>{{ $breadcrumb['name'] }}</span>
                </a>
              </li>
            @else
              <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['name'] }}</li>
            @endif
          @endforeach
        @endif
      </ol>
    </nav>
    @if($create_btn_name)
      <a href="{{ route($btn_route) }}" class="btn btn-info">{{ $create_btn_name }}</a>
    @endif
  </div>
@endif
