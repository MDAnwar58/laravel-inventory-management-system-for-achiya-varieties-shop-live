@extends('layouts.admin-layout')
@section('title', '- Product Show')

@push('style')
  <x-admin.product.styles />
  <style>
    .bdt {
        font-family: 'Noto Sans Bengali', sans-serif;
        margin-right: 0.1rem;
    }
  </style>
@endpush

@section('content')
  <x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Product Details</h2>
    <div class="action-buttons d-flex">
      <a href="{{ route('admin.product.edit', $data->id) }}" class="btn btn-outline-primary"><i
          class="fas fa-edit me-2"></i>Edit</a>
      <!-- <button class="btn btn-outline-secondary"><i class="fas fa-copy me-2"></i>Duplicate</button> -->
      <form action="{{ route('admin.product.show.on.delete', $data->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash me-2"></i>Delete</button>
      </form>
    </div>
  </div>

  <div class="row">
    <!-- Left Column - Product Images -->
    <div class="col-xl-5">
      @if ($data->image)
        <div class="info-card">
        <div class="position-relative">
          <img src="{{ $data->image }}" alt="Product Image" class="product-image {{ $data->stock !== 0 ? '' : '' }}" id="mainImage">
          @if($data->stock === 0)
          <div class=" position-absolute top-0 start-0 w-100 rounded-3 d-flex justify-content-center align-items-center" style="background-color: rgba(255, 255, 255, 0.65); height: 100%;">
            <i class="fa-solid fa-ban text-muted" style="font-size: 7rem;"></i>
          </div>
          @endif
        </div>
          <!-- Image Gallery -->
          <!-- <div class="d-flex gap-2 mt-3">
                  <img src="https://via.placeholder.com/80x80/3498db/ffffff?text=1" alt="Gallery 1"
                    class="gallery-thumbnail active" onclick="changeImage(this)">
                  <img src="https://via.placeholder.com/80x80/e74c3c/ffffff?text=2" alt="Gallery 2" class="gallery-thumbnail"
                    onclick="changeImage(this)">
                  <img src="https://via.placeholder.com/80x80/27ae60/ffffff?text=3" alt="Gallery 3" class="gallery-thumbnail"
                    onclick="changeImage(this)">
                  <img src="https://via.placeholder.com/80x80/f39c12/ffffff?text=4" alt="Gallery 4" class="gallery-thumbnail"
                    onclick="changeImage(this)">
                </div> -->
        </div>
      @endif

      <!-- Quick Stats -->
      <div class="info-card">
        <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Quick Stats</h5>
        <div class="row text-center">
          <div class="col-6 mb-3">
            <h4 class="text-primary mb-1">{{ $total_solded }}</h4>
            <small class="text-muted">Total Sold</small>
          </div>
          <div class="col-6 mb-3">
            <h4 class="text-info mb-1">
            <span class="bdt">৳</span>{{ number_format($total_wholesale_earnings, 2) }}
            </h4>
            <small class="text-muted">Total Wholesale Sales Earnings</small>
          </div>
          <div class="col-6 mb-3">
            <h4 class="text-info mb-1">
            <span class="bdt">৳</span>{{ number_format($total_retail_earnings, 2) }}
            </h4>
            <small class="text-muted">Total Retail Sales Earnings</small>
          </div>
          <div class="col-6 mb-3">
            <h4 class="text-info mb-1">
            <span class="bdt">৳</span>{{ number_format($total_profits, 2) }}
            </h4>
            <small class="text-muted">Total Profits</small>
          </div>
          <div class="col-6 mb-3">
            <h4 class="{{ $stock > 0 && $stock <= $data->low_stock_level
    ? 'text-warning'
    : ($stock > 0
      ? 'text-success'
      : 'text-danger') }} mb-1">
              {{ $current_stock }}
            </h4>

            <small class="{{ $stock === 0 ? 'text-danger' : 'text-muted' }}">
              @if ($stock > 0 && $stock <= $data->low_stock_level)
                Current Stock Low
              @elseif ($stock > 0)
                Current Stock Available
              @else
                Out of Stock
              @endif
            </small>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column - Product Details -->
    <div class="col-xl-7">
      <div class="info-card">
        <!-- Product Title and Status -->
        <div class="d-flex justify-content-between align-items-start mb-3">
          <div>
            <h3 class="mb-2 fs-2">{{ $data->name }}</h3>
            <p class="text-muted mb-0">SKU: #{{ $data->sku }}</p>
            <div class="fs-6">
              <strong>Type:</strong> {{ $data->item_type->name }}
            </div>
          </div>
          <span class="badge status-badge {{ $data->status == 'active' ? 'bg-success' : 'bg-danger' }}">
            @if ($data->status == 'active')
              <i class="fas fa-check-circle me-1"></i>
              Active
            @else
              <i class="fas fa-times-circle me-1"></i>
              Deactive
            @endif
          </span>
        </div>

        <!-- Price Information -->
        <div class="d-flex align-items-center mb-4">
          <span class="price-tag">
            <span class="bdt">৳</span>
            {{ $data->discount_price ? $data->discount_price : $data->price }}
          </span>
          @if ($data->discount_price)
            <span class="text-muted text-decoration-line-through ms-3">
              <i class="fa-solid fa-bangladeshi-taka-sign fs-6" style="margin-right: -0.37rem;"></i>
              {{ $data->price }}
            </span>
          @endif
          <!-- <span class="discount-badge">25% OFF</span> -->
        </div>

        <!-- Product Tabs -->
        <ul class="nav nav-pills mb-4" id="productTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="pill" data-bs-target="#overview"
              type="button" role="tab">Overview</button>
          </li>
          <!-- <li class="nav-item" role="presentation">
  <button class="nav-link" id="specifications-tab" data-bs-toggle="pill" data-bs-target="#specifications"
    type="button" role="tab">Specifications</button>
</li> -->
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="inventory-tab" data-bs-toggle="pill" data-bs-target="#inventory" type="button"
              role="tab">Inventory</button>
          </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="productTabsContent">
          <!-- Overview Tab -->
          <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <h6>Product Description</h6>
            {!! $data->desc !!}

            <div class="row mt-4">
              <div class="col-12 fs-4">
                <strong>Brand:</strong> {{ $data->brand?->name ?? 'N/A' }}
              </div>
              <div class="col-12 fs-4">
                <strong>Category:</strong> {{ $data->category?->name ?? 'N/A' }}
              </div>
              <div class="col-12 fs-4">
                <strong>Sub Category:</strong> {{ $data->sub_category?->name ?? 'N/A' }}
              </div>
            </div>
          </div>

          <!-- Specifications Tab -->
<!-- <div class="tab-pane fade" id="specifications" role="tabpanel">
<table class="table specs-table">
  <tbody>
    <tr>
      <th>Connectivity</th>
      <td>Bluetooth 5.0, 3.5mm Jack</td>
    </tr>
    <tr>
      <th>Battery Life</th>
      <td>30 hours (ANC Off), 24 hours (ANC On)</td>
    </tr>
    <tr>
      <th>Charging Time</th>
      <td>2 hours full charge, 15 min quick charge</td>
    </tr>
    <tr>
      <th>Weight</th>
      <td>280g</td>
    </tr>
    <tr>
      <th>Driver Size</th>
      <td>40mm Dynamic Drivers</td>
    </tr>
    <tr>
      <th>Frequency Response</th>
      <td>20Hz - 20kHz</td>
    </tr>
    <tr>
      <th>Microphone</th>
      <td>Built-in with CVC 8.0 Noise Cancellation</td>
    </tr>
    <tr>
      <th>Warranty</th>
      <td>2 Years International Warranty</td>
    </tr>
  </tbody>
</table>
</div> -->

          <!-- Inventory Tab -->
          <div class="tab-pane fade" id="inventory" role="tabpanel">
            <div class="row">
              <div class="col-md-6">
                @php
                  function stock_type($limit, $type){
                      if ($type !== 'none') {
                          if ($type === 'kg'){
                            if ($limit < 1)return 'gm'; 
                            else return 'kg';
                          }elseif ($type === 'ft'){
                            if ($limit < 1)return 'inchi'; 
                            else return 'ft';
                          } elseif ($type === 'yard') {
                            if ($limit < 1)return 'inchi'; 
                            else return 'yard';
                          } else {
                            if ($limit < 1)return 'inchi'; 
                            else return 'm';
                          }
                      } else return ' pcs';
                  }
                @endphp
                <h6 class="h4">Stock Information</h6>
                <p><strong>Current Stock:</strong> {{ $current_stock }}
                </p>
                <p><strong>Min. Perchase:</strong> {{  $data->purchase_limit ? $data->purchase_limit : 0 }}{{ $data->purchase_limit ? stock_type($data->purchase_limit, $data->stock_w_type) : '' }}</p>
                <!-- <p><strong>Available:</strong> 37 units</p> -->
                <p><strong>Alert For Stock Under:</strong> {{  $data->purchase_limit ? $data->low_stock_level : 0 }}{{  $data->purchase_limit ? stock_type($data->purchase_limit, $data->stock_w_type) : '' }}</p>
              </div>
              <div class="col-md-6">
                <h6 class="h4">Pricing</h6>
                <p><strong>Cost Price:</strong> <span class="bdt">৳</span>{{ $data->cost_price }}</p>
                <p><strong>Wholesale Price:</strong> <span class="bdt">৳</span>{{ $data->price ?? 'N/A' }}</p>
                <p><strong>Retail Price:</strong> <span class="bdt">৳</span>{{ $data->retail_price ?? 'N/A' }}
                </p>
                <!-- <p><strong>Profit Margin:</strong> {{ $data->profit_margin }}</p> -->
                <!-- <p><strong>Last Updated:</strong> {{ $data->updated_at }}</p> -->
              </div>
            </div>

            @if ($data->stock_updated && $data->stock_updated_at || $data->change_price && $data->change_price_updated_at || $data->sold_units && $data->solded_at)
              <div class="mt-4">
                <h6 class="h4 text-secondary">Recent Activity</h6>
                <ul class="list-group list-group-flush">
                  @if ($data->stock_updated && $data->stock_updated_at)
                    <li class="list-group-item d-flex justify-content-between">
                      <span>{{ $data->stock_updated }}</span>
                      <small class="text-muted">{{ $data->stock_updated_at->diffForHumans() }}</small>
                    </li>
                  @endif
                  @if ($data->change_price && $data->change_price_updated_at)
                    <li class="list-group-item d-flex justify-content-between">
                      <span>{{ $data->change_price }}</span>
                      <small class="text-muted">{{ $data->change_price_updated_at->diffForHumans() }}</small>
                    </li>
                  @endif
                  @if ($data->sold_units && $data->solded_at)
                    <li class="list-group-item d-flex justify-content-between">
                      <span>{{ $data->sold_units }}</span>
                      <small class="text-muted">{{ $data->solded_at->diffForHumans() }}</small>
                    </li>
                  @endif
                </ul>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('script')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const submitBtn = document.querySelector('.submit-btn')
      const spinner = document.getElementById('spinner-border')
      const imageInput = document.getElementById('image')
      const imagePrev = document.getElementById('image-prev')
      const removeBtn = document.getElementById('remove-btn')
      const imagePrevDiv = document.getElementById('image-prev-div')
      imageInput.value = ''

      imageInput.addEventListener('change', function (e) {
        const file = e.target.files[0]
        const reader = new FileReader()
        reader.onload = function (e) {
          removeBtn.classList.remove('d-none')
          imagePrev.classList.remove('d-none')
          imagePrev.src = e.target.result
          imagePrevDiv.classList.add('pb-3')
        }
        reader.readAsDataURL(file)
      })

      removeBtn.addEventListener('click', function () {
        imagePrev.classList.add('d-none')
        removeBtn.classList.add('d-none')
        imagePrevDiv.classList.remove('pb-2')
        imageInput.value = ''
      })

      submitBtn.addEventListener('click', function () {
        spinner.classList.remove('d-none')
        submitBtn.classList.add('disabled')
      })

      // Initialize tooltips if needed
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    })
    function changeImage(thumbnail) {
      // Remove active class from all thumbnails
      // document.querySelectorAll('.gallery-thumbnail').forEach(img => {
      //   img.classList.remove('active');
      // });

      // Add active class to clicked thumbnail
      thumbnail.classList.add('active');

      // Update main image
      const mainImage = document.getElementById('mainImage');
      const newSrc = thumbnail.src.replace('80x80', '400x300');
      mainImage.src = newSrc;
    }
  </script>
@endpush
