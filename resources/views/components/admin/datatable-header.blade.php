@props([
  'fitler_option' => true
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
    @if($fitler_option)
    <div class="dropdown position-relative">
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
      </ul>
    </div>
    @endif
  </div>
</div>
