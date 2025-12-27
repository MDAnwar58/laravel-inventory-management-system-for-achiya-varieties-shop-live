@extends('layouts.admin-layout')
@section('title', '- Product Create')

@push('style')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
<x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header pb-0">
                <h5 class="card-title mb-0 fs-4 text-secondary">Product Added</h5>
            </div>
            <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data" class="card-body">
                @csrf
                <div class="pb-3">
                    <label for="name" class="form-label mb-1 text-muted">Product Name</label>
                    <div class="d-flex align-items-center gap-1">
                        <input type="text" name="name" class="px-3 py-2 fs-5 form-control  custom-input  input" />
                        <span class="text-danger fs-3">*</span>
                    </div>
                    <x-error fieldName="name" />
                </div>
                <div class="pb-3">
                    <div class="row">
                        <div class="col-xl-3 col-md-4 col-sm-6">
                            <label for="price" class="form-label mb-1 text-muted">Wholesale Price</label>
                            <div class="d-flex align-items-center gap-1">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-bangladeshi-taka-sign fs-6"></i>
                                    </span>
                                    <input type="text" name="price" id="price" class="px-3 py-2 fs-5 form-control  custom-input  input" />
                                </div>
                                <span class="text-warning fs-3">*</span>
                            </div>
                            <x-error fieldName="price" />
                        </div>
                        {{-- <div class="col-xl-3 col-md-4 col-sm-6 mt-sm-0 mt-3">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-bangladeshi-taka-sign fs-6"></i>
                                </span>
                                <input type="number" name="discount_price" class="px-3 py-2 fs-5 form-control  custom-input  input" placeholder="Discount price" />
                            </div>
                        </div> --}}
                        <div class="col-xl-3 col-md-4 col-sm-6 mt-sm-0 mt-3">
                            <label for="retail_price" class="form-label mb-1 text-muted">Retail Price</label>
                            <div class="d-flex align-items-center gap-1">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-bangladeshi-taka-sign fs-6"></i>
                                    </span>
                                    <input type="text" name="retail_price" id="retail_price" class="px-3 py-2 fs-5 form-control  custom-input  input" />
                                </div>
                                <span class="text-warning fs-3">*</span>
                            </div>
                            <x-error fieldName="retail_price" />
                        </div>
                        <div class="col-xl-3 col-md-4 col-sm-6 mt-md-0 mt-3">
                            <label for="cost_price" class="form-label mb-1 text-muted">Cost Price</label>
                            <div class="d-flex align-items-center gap-1">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-bangladeshi-taka-sign fs-6"></i>
                                    </span>
                                    <input type="text" name="cost_price" id="cost_price" class="px-3 py-2 fs-5 form-control  custom-input  input" />
                                </div>
                                <span class="text-danger fs-3">*</span>
                            </div>
                            <x-error fieldName="cost_price" />
                        </div>
                        <div class="col-xl-3 col-md-4 col-sm-6 mt-xl-0 mt-3">
                            <label for="stock" class="form-label mb-1 text-muted">Stock</label>
                            <div class="d-flex align-items-center gap-1">
                                <div class="input-group">
                                    <span class="input-group-text fs-4 fw-bold">
                                        <i class="fa-solid fa-hashtag fs-6"></i>
                                    </span>
                                    <input type="text" name="stock" id="stock" class="px-3 py-2 fs-5 form-control  custom-input  input" />
                                </div>
                                <span class="text-warning fs-3">*</span>
                            </div>
                            <x-error fieldName="stock" />
                        </div>
                        <div class="col-xl-3 col-md-4 col-sm-6 mt-3">
                            <label for="stock_w" class="form-label mb-1 text-muted">Stock Weight/Fit/Yard</label>
                            <div class="d-flex align-items-center gap-1">
                                <div class="input-group">
                                    <span class="input-group-text fs-4 fw-bold">
                                        <i class="fa-solid fa-hashtag fs-6"></i>
                                    </span>
                                    <input type="text" name="stock_w" id="stock_w" class="px-3 py-2 fs-5 form-control  custom-input  input" step="0.01" disabled />
                                    <select name="stock_w_type" id="stock_w_type" class=" border-secondary-subtle text-secondary rounded-end-2 rounded-start-0 bg-light border-1">
                                        <option value="none">None</option>
                                        <option value="kg">Kg</option>
                                        <option value="ft">Ft</option>
                                        <option value="yard">Yard</option>
                                        <option value="m">Meter</option>
                                    </select>
                                </div>
                                <span class="text-warning fs-3">*</span>
                            </div>
                            <x-error fieldName="stock" />
                        </div>
                        <div class="col-xl-3 col-md-4 col-sm-6 mt-3">
                            <label for="low_stock_level" class="form-label mb-1 text-muted">Low Stock Level</label>
                            <div class="d-flex align-items-center gap-1">
                                <div class="input-group">
                                    <span class="input-group-text fs-4 fw-bold">
                                        <i class="fa-solid fa-hashtag fs-6"></i>
                                    </span>
                                    <input type="text" name="low_stock_level" id="low_stock_level" class="px-3 py-2 fs-5 form-control  custom-input  input" />
                                </div>
                                <span class="text-warning fs-3">*</span>
                            </div>
                            <x-error fieldName="low_stock_level" />
                        </div>
                        <div class="col-xl-3 col-md-4 col-sm-6 mt-3">
                            <label for="purchase_limit" class="form-label mb-1 text-muted">Purchase Limit</label>
                            <div class="input-group">
                                <span class="input-group-text fs-4 fw-bold">
                                    <i class="fa-solid fa-hashtag fs-6"></i>
                                </span>
                                <input type="text" name="purchase_limit" id="purchase_limit" class="px-3 py-2 fs-5 form-control  custom-input  input" />
                            </div>
                            <x-error fieldName="purchase_limit" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="pb-3">
                            <label for="item_type_id" class="form-label mb-1 text-muted">Item Type</label>
                            <div class="d-flex align-items-center gap-1">
                                <select name="item_type_id" class="px-3 py-2 fs-5 form-select focus-ring-none">
                                    <option value="" class=" text-muted">Choose item type</option>

                                    @if ($item_types->count() > 0)
                                    @foreach ($item_types as $item_type)
                                    <option value="{{ $item_type->id }}">{{ $item_type->name }}</option>
                                    @endforeach
                                    @else
                                    <option value="">Not item type found</option>
                                    @endif
                                </select>

                                <span class="text-warning fs-3">*</span>
                            </div>
                        </div>
                        <x-error fieldName="item_type_id" />
                    </div>
                    <div class="col-md-6">
                        <label for="brand_id" class="form-label mb-1 text-muted">Brand</label>
                        <div class="pb-3">
                            <select name="brand_id" class="px-3 py-2 fs-5 form-select focus-ring-none">
                                <option value="" class=" text-muted">Choose brand</option>

                                @if ($brands->count() > 0)
                                @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                                @else
                                <option value="">Not brand found</option>
                                @endif
                            </select>
                            <x-error fieldName="brand_id" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="pb-3">
                            <label for="brand_id" class="form-label mb-1 text-muted">Category</label>
                            <div class="d-flex align-items-center gap-1">
                                <select name="category_id" class="px-3 py-2 fs-5 form-select focus-ring-none">
                                    <option value="" class=" text-muted">Choose category</option>

                                    @if ($categories->count() > 0)
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                    @else
                                    <option value="">Not category found</option>
                                    @endif
                                </select>

                                <span class="text-warning fs-3">*</span>
                            </div>
                            <x-error fieldName="category_id" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="sub_category_id" class="form-label mb-1 text-muted">Sub Category</label>
                        <div class="pb-3">
                            <div class="d-flex align-items-center gap-1">
                                <select name="sub_category_id" class="px-3 py-2 fs-5 form-select focus-ring-none">
                                    <option value="" class=" text-muted">Choose sub category</option>

                                    @if ($sub_categories->count() > 0)
                                    @foreach ($sub_categories as $sub_category)
                                    <option value="{{ $sub_category->id }}">{{ $sub_category->name }}</option>
                                    @endforeach
                                    @else
                                    <option value="">Not sub category found</option>
                                    @endif
                                </select>

                                <span class="text-warning fs-3">*</span>
                            </div>
                            <x-error fieldName="sub_category_id" />
                        </div>
                    </div>
                </div>

                <div class="pb-3">
                    <label for="sub_category_id" class="form-label mb-1 text-muted">Status</label>
                    <select name="status" class="px-3 py-2 fs-5 form-select custom-select focus-ring-none">
                        <option value="active">Active</option>
                        <option value="deactive">Deactive</option>
                    </select>
                    <x-error fieldName="status" />
                </div>
                <div class="pb-3">
                    <label for="image" class="form-label mb-1 text-muted">Image</label>
                    <div id="image-prev-div" class="d-flex">
                        <div class="position-relative">
                            <button type="button" id="remove-btn" class="btn btn-sm btn-outline-danger rounded-3 position-absolute top-0 end-0 me-3 mt-2 d-none"><i class="feather-sm" data-feather="x"></i></button>
                            <img id="image-prev" class="rounded-circle d-none" alt="Image" style="width: 151px; height: 151px;" />
                        </div>
                    </div>
                    <input type="file" id="image" name="image" class="px-3 py-2 fs-5 form-control focus-ring-none" />
                    <x-error fieldName="image" />
                </div>
                <div class="pb-3">
                    <textarea id="desc" name="desc"></textarea>
                    <x-error fieldName="desc" />
                </div>
                <div class="text-end">
                    <button type="submit" class="submit-btn btn btn-primary fs-4 px-3">
                        <span class="fw-semibold">Save</span>
                        <div id="spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>
    $(document).ready(function() {
        $('#desc').summernote({
            height: 250
        });
    });

//    function calculatePriceFromFtInches(ftInchesInput, pricePerFt) {
//    const inchPerFt = 12;

//    const feet = Math.floor(ftInchesInput);
//    const inchesDecimal = ftInchesInput - feet;

    // Treat .XX as inches directly
//    const inches = Math.round(inchesDecimal * 100);

//    const totalInches = feet * inchPerFt + inches;
//    const pricePerInch = pricePerFt / inchPerFt;
//
//    return Math.round(totalInches * pricePerInch);
//}
//console.log(calculatePriceFromFtInches(3.06, 50));

    const writeOnlyNumberInInput = (input) => input.value = input.value.replace(/\D/g, '');
    // write number and decimals and decimals 3
    const writeOnlyNumberAndDecimalInput = (input, decimals_length = 3) => {
            const val = input.value;
        // 1. strip every character that is not a digit or dot
            let cleaned = val.replace(/[^0-9.]/g, '');

            // 2. keep only the first dot
            const parts = cleaned.split('.');
            if (parts.length > 2) cleaned = parts[0] + '.' + parts.slice(1).join('');

            // 3. allow at most 3 decimals
            if (parts[1] && parts[1].length > decimals_length) {
                cleaned = parts[0] + '.' + parts[1].slice(0, decimals_length);
            }

            // 4. put the cleaned value back (no cursor jump)
            if (cleaned !== val) {
                input.value = cleaned;
            }
    }

    const writeOnlyNumberAndDecimalWithFtOrInchiCalCulateInput = (input, decimals_length = 2, maxDecimal = 11) => {
        let val = input.value;

        // 1. strip every character that is not a digit or dot
        let cleaned = val.replace(/[^0-9.]/g, '');

        // 2. keep only the first dot
        const parts = cleaned.split('.');
        if (parts.length > 2) cleaned = parts[0] + '.' + parts.slice(1).join('');

        // 3. limit decimals to decimals_length
        if (parts[1]) {
            let decimalPart = parts[1].slice(0, decimals_length);

            // 4. enforce max decimal number (e.g., .11)
            if (parseInt(decimalPart) > maxDecimal) {
                decimalPart = maxDecimal.toString().padStart(decimals_length, '0');
            }

            cleaned = parts[0] + '.' + decimalPart;
        }

        // 5. put the cleaned value back
        if (cleaned !== val) input.value = cleaned;
    };


    document.addEventListener('DOMContentLoaded', function() {
        const submitBtn = document.querySelector('.submit-btn')
        const spinner = document.getElementById('spinner-border')
        const imageInput = document.getElementById('image')
        const imagePrev = document.getElementById('image-prev')
        const removeBtn = document.getElementById('remove-btn')
        const imagePrevDiv = document.getElementById('image-prev-div')
        const price = document.getElementById('price')
        const retailPrice = document.getElementById('retail_price')
        const costPrice = document.getElementById('cost_price')
        const stock = document.getElementById('stock')
        const stockW = document.getElementById('stock_w')
        const stockWType = document.getElementById('stock_w_type')
        const lowStockLevel = document.getElementById('low_stock_level')
        const purchaseLimit = document.getElementById('purchase_limit')
        
        price.value = ''
        retailPrice.value = ''
        costPrice.value = ''
        stock.value = ''
        stockW.value = ''
        imageInput.value = ''

        price.addEventListener('input', e => {
            writeOnlyNumberAndDecimalInput(e.currentTarget, 2)
        })
        retailPrice.addEventListener('input', e => {
            writeOnlyNumberAndDecimalInput(e.currentTarget, 2)
        })
        costPrice.addEventListener('input', e => {
            writeOnlyNumberAndDecimalInput(e.currentTarget, 2)
        })

        stockWType.addEventListener('change', function(e) {
            const value = e.currentTarget.value;
            if (value !== 'none') {
                stock.disabled = true;
                stockW.disabled = false;
            } else {
                stock.disabled = false;
                stockW.disabled = true;
            }
        })

        stock.addEventListener('input', e => {
            writeOnlyNumberInInput(e.currentTarget)
        });

        stockW.addEventListener('input', e => {
            if (stockWType.value === 'ft')writeOnlyNumberAndDecimalWithFtOrInchiCalCulateInput(e.currentTarget, 2, 11)
            else if (stockWType.value === 'yard')writeOnlyNumberAndDecimalWithFtOrInchiCalCulateInput(e.currentTarget, 2, 36)
            else if (stockWType.value === 'm')writeOnlyNumberAndDecimalWithFtOrInchiCalCulateInput(e.currentTarget, 2, 39)
            else writeOnlyNumberAndDecimalInput(e.currentTarget, 3)
        });

        lowStockLevel.addEventListener('input', e => {
            writeOnlyNumberInInput(e.currentTarget)
        });

        purchaseLimit.addEventListener('input', e => {
            writeOnlyNumberAndDecimalInput(e.currentTarget, 2)
        });

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0]
            const reader = new FileReader()
            reader.onload = function(e) {
                removeBtn.classList.remove('d-none')
                imagePrev.classList.remove('d-none')
                imagePrev.src = e.target.result
                imagePrevDiv.classList.add('pb-3')
            }
            reader.readAsDataURL(file)
        })

        removeBtn.addEventListener('click', function() {
            imagePrev.classList.add('d-none')
            removeBtn.classList.add('d-none')
            imagePrevDiv.classList.remove('pb-3')
            imageInput.value = ''
        })

        submitBtn.addEventListener('click', function() {
            spinner.classList.remove('d-none')
            submitBtn.classList.add('disabled')
        })
    })

    
</script>
@endpush
