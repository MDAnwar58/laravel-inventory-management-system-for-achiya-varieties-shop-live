<div class="col-lg-12">
    <div class="card card-body">
        <div class="row billed_row px-3">
            <div class="col-8">
                <h5 class="text-uppercase">billed to</h5>
                <div class="text-muted text-info">Name: <span id="CName"></span></div>
                <div class="text-muted text-info">Phone Numner: <span id="CPhone"></span></div>
                <div class="text-muted text-info">Address: <span id="CAddress"></span></div>
            </div>
            <div class="col-4">
                <div class="">
                    <img src="{{ url('assets/img/logo/logo.png') }}" style="width: 40px; height: 15px;" alt="" />
                    <div class="h6">Invoice</div>
                    <div class="text-muted text-info">Date: <span id="invoiceDate"></span></div>
                </div>
            </div>
            <hr class="mt-3">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-hover" id="billed_table">
                        <thead>
                            <tr class="">
                                <th class="text-muted fw-normal">Product</th>
                                <th class="text-muted fw-normal">Price</th>
                                <th class="text-muted fw-normal">Qty</th>
                                <th class="text-muted fw-normal">Total</th>
                                <th class="text-muted fw-normal">Remove</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceList"></tbody>
                    </table>
                </div>
            </div>
            <hr class="mt-3">
            <div class="col-md-12">
                <h6 class="text-uppercase text-amount">Total: $<span id="total"></span></h6>
                <h6 class="text-uppercase text-amount">Payable: $<span id="payable"></span></h6>
                <h6 class="text-uppercase text-amount">vat(5%): $<span id="vat"></span></h6>
                <h6 class="text-uppercase text-amount">Discount: $<span id="discount"></span></h6>
                <h6 class="text-uppercase text-amount">Due Amount: $<span id="discount"></span></h6>
                <div class="form-group mb-2">
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
                </div>
                <div class="form-group">
                    <label for="paid_amount" class="text-muted text-discount">Paid Amount</label><br>
                    <div class="input-group" style="width: 135px;">
                        <input type="number" id="paid_amount" name="paid_amount" value="0"
                            class="text-center form-control text-secondary">
                        <button type="button"
                            class="paid-amount-edit-btn btn btn-sm btn-primary text-white text-secondary">
                            <!-- edit icon pen -->
                            <i class="fa fa-pencil" id="paid-amount-edit-icon" aria-hidden="true"></i>
                            <!-- close icon -->
                            <i class="fa fa-times d-none" id="paid-amount-close-icon" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" onclick="createInvoice()"
                        class="btn btn-danger mt-2 px-5 confirm_button text-uppercase">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
