@props([
    'printing_content' => null,
    'data' => null
])
<div class="col-lg-12">
    <div class="card card-body">
        <div class="row billed_row px-3">
            <div class="col-xxl-9 col-md-8 col-sm-7 col-12 order-sm-1 order-2 text-sm-start text-center pt-sm-0 pt-3">
                <h5 class="text-uppercase">billed to</h5>
                <div class="text-muted text-info">Order Date: <span id="invoiceDate"></span></div>
                <div class="text-muted">Order Id: <span>#{{ $data->order_number }}</span></div>
                <input type="hidden" id="CId" name="customer_id">
                <div class="text-muted text-info">Name: <span id="CName"></span></div>
                <div class="text-muted text-info">Phone Numner: <span id="CPhone"></span></div>
                <div class="text-muted text-info">Address: <span id="CAddress"></span></div>
            </div>
            <div class="col-xxl-3 col-md-4 col-sm-5 col-12 order-sm-2 order-1">
                <div class="">
                    <div class="d-flex gap-1 align-items-center justify-content-sm-start justify-content-center">
                        <img src="{{ asset('favicon_io2/android-chrome-512x512.png') }}" width="40" alt="" />
                        <div class="gradient-app-logo" style="font-size: 1.3rem;">
                            আছিয়া <div style="font-size: 10px; margin-top: -0.5rem;">ভ‍্যারাইটিস শপ</div>
                        </div>
                    </div>
                    <div class="text-muted text-wrap mt-1 text-sm-start text-center" style="white-space: normal;">
                            <span class="d-sm-inline-block d-none">Phone:</span>
                            <span class="text-start">
                                {{ $printing_content?->phone_number }}, {{ $printing_content?->phone_number2 }}
                            </span>
                        </div>

                        <div class="text-muted text-wrap text-sm-start text-center" style="white-space: normal;">
                            <span class="d-sm-inline-block d-none">Location:</span>
                            <span class="text-start">
                                {{ $printing_content?->location }}
                            </span>
                        </div>
                </div>
            </div>
            <hr class="mt-3 order-3">
            <div class="col-md-12 order-4">
                <input type="hidden" name="products" id="products-input">
                <div class="table-responsive">
                    <table class="table table-hover" id="billed_table">
                        <thead>
                            <tr class="">
                                <th class="text-muted fw-normal">Product</th>
                                <th class="text-muted fw-normal">Price</th>
                                <th class="text-muted fw-normal">Quantity/Weights/Foot/Yard/Meter</th>
                                <th class="text-muted fw-normal">Total</th>
                                <th class="text-muted fw-normal">Remove</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceList"></tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12 order-5">
                <hr class="mt-3">

                <div id="paid-amount-div" class="form-group d-none mb-3">
                    <label class="text-muted text-discount">Paid Amount</label><br>
                    <div class="input-group" style="width: 135px;">
                        <button type="button"
                            class="paid-amount-edit-btn btn btn-sm btn-light border text-white text-secondary">
                            <i class="fa-solid fa-bangladeshi-taka-sign text-dark-emphasis"
                                style="font-size: .85rem;"></i>
                        </button>
                        <input type="text" id="paid-amount-input" name="paid_amount"
                            class="text-center form-control text-secondary">
                    </div>
                </div>
                <!-- <h6 class="text-uppercase text-amount">Sub Total:
                    <i class="fa-solid fa-bangladeshi-taka-sign text-dark-emphasis"
                        style="font-size: .85rem;margin-right: -0.33rem;"></i>
                    <span id="subtotal" class="text-dark">0.00</span>
                </h6> -->
                <!-- <h6 class="text-uppercase text-amount">vat/Tax(5%):
                    <span id="vat" class="text-dark">5</span>%
                </h6> -->
                <h6 class="text-uppercase text-amount">Total:
                    <span class="bdt">৳</span>
                    <span id="total" class="text-dark">0.00</span>
                    <input type="hidden" name="total_amount" id="total-amount-input">
                    <input type="hidden" name="sub_total_amount" id="sub-total-amount-input">
                </h6>
                <h6 id="paid-amount-el" class="text-uppercase text-amount d-none">Paid Amount:
                    <span class="bdt">৳</span>
                    <span id="paid-amount">0.00</span>
                </h6>
                <h6 id="due-amount-el" class="text-uppercase text-amount d-none">Due Amount:
                    <span class="bdt">৳</span>
                    <span id="due-amount">0.00</span>
                    <input type="hidden" id="due-amount-input" name="due_amount">
                </h6>
                <!-- <div class="form-group mb-2">
                    <label for="discount" class="text-muted text-discount">Discount(%)</label><br>
                    <div class="input-group" style="width: 155px;">
                        <input type="text" id="discountP" value="0" onchange="DiscountChange()"
                            class="text-center form-control text-secondary">

                        <button type="button" class="decrement-btn btn btn-sm btn-light fs-5 text-muted">
                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                        </button>
                        <button type="button"
                            class="increment-btn btn btn-sm btn-light fs-5 text-muted border-start rounded-end">
                            <i class="fa fa-chevron-up" aria-hidden="true"></i>
                        </button>
                    </div>
                </div> -->
                <div class="d-flex justify-content-end gap-2">
                    {{-- <button type="button" id="reset-all-btn"
                        class="btn btn-danger px-2 text-uppercase d-flex justify-content-center align-items-center">
                        <i class="fa-solid fa-rotate fs-4"></i>
                    </button> --}}
                    <button type="submit" class="btn btn-success text-uppercase fs-4 fw-semibold submit-btn">
                        <span class="fw-semibold">Order Update</span>
                        <div id="spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
