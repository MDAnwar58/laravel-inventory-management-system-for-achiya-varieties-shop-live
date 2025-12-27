@props([
  'item_types' => [],
  'brands' => [],
  'categories' => [],
  'sub_categories' => [],
])
<div class="datatable-header d-flex flex-wrap justify-content-between align-items-center gap-2">
  <div class="entries-info d-flex align-items-center">
    Show<select class="entries-select mx-2" id="parPage">
      <option value="5">5</option>
      <option value="10">10</option>
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
    </select>entries
  </div>

  <div class="d-flex flex-wrap gap-2 justify-content-sm-start justify-content-center align-items-center">
    <form id="search-form" class="d-flex" action="" method="GET">
      <input type="text" class="search-input form-control rounded-end-0" id="searchInput" placeholder="Search..." />
      <button type="button" class="search-btn btn btn-sm btn-secondary rounded-start-0 rounded"><i
          class="fa-solid fa-magnifying-glass fs-4"></i></button>
    </form>

    <!-- Filter button outside the form -->

    <div class="dropdown">
      <button type="button" class="btn btn-outline-secondary py-2 px-2 h-100" id="filterButton" type="button"
        data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-filter fs-4"></i></button>

      <ul class="dropdown-menu p-2">
        <li>
          <select class="form-select" id="filter-select-field" aria-label="Default select example">
            <option disabled>Filter Status</option>
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="deactive">Deactive</option>
          </select>
        </li>
        <li class="pt-2">
          <label class="text-muted fs-5">Filter Stock</label>
          <select class="form-select" id="filter-stock-select-field" aria-label="Default select example">
            <option value="">All</option>
            <option value="low stock">Low Stock</option>
            <option value="in stock">In Stock</option>
          </select>
        </li>
        <li class="pt-2">
          <label class="text-muted fs-5">Filter With Item Type</label>
          <select class="form-select" id="item-type-select-field" aria-label="Default select example">
            <option value="">All</option>

            @if ($item_types->count() > 0)
              @foreach ($item_types as $item_type)
                <option value="{{ $item_type->id }}">{{ $item_type->name }}</option>
              @endforeach
            @else
              <option value="">No Item Type Found</option>
            @endif
          </select>
        </li>
        <li class="pt-2">
          <label class="text-muted fs-5">Filter With Brand</label>
          <select class="form-select" id="brand-select-field" aria-label="Default select example">
            <option value="">All</option>

            @if ($brands->count() > 0)
              @foreach ($brands as $brand)
                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
              @endforeach
            @else
              <option value="">No Brand Found</option>
            @endif
          </select>
        </li>
        <li class="pt-2">
          <label class="text-muted fs-5">Filter With Category</label>
          <select class="form-select" id="category-select-field" aria-label="Default select example">
            <option value="">All</option>

            @if ($categories->count() > 0)
              @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
              @endforeach
            @else
              <option value="">No Category Found</option>
            @endif
          </select>
        </li>
        <li class="pt-2">
          <label class="text-muted fs-5">Filter With Sub Category</label>
          <select class="form-select" id="sub-category-select-field" aria-label="Default select example">
            <option value="">All</option>

            @if ($sub_categories->count() > 0)
              @foreach ($sub_categories as $sub_category)
                <option value="{{ $sub_category->id }}">{{ $sub_category->name }}</option>
              @endforeach
            @else
              <option value="">No Sub Category Found</option>
            @endif
          </select>
        </li>
      </ul>
    </div>
  </div>
</div>
