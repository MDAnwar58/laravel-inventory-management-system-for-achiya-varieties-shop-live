<div class="modal fade" id="customerAddModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold text-secondary" id="staticBackdropLabel">Add New Customer</h1>
                <button type="button" class="btn-close add-customer-modal-close-btn"></button>
            </div>
            <div class="modal-body">
                <div class="pb-3">
                    <div class="d-flex align-items-center gap-1">
                        <input type="text" id="name" name="name"
                            class="px-3 py-2 fs-5 form-control  custom-input  input" placeholder="Name" />
                        <span class="text-danger fs-3">*</span>
                    </div>
                    <span id="name-error" class="text-danger fs-5 fw-medium"></span>
                </div>
                <div class="pb-3">
                    <div class="d-flex align-items-center gap-1">
                        <input type="text" id="phone" name="phone"
                            class="px-3 py-2 fs-5 form-control  custom-input  input" placeholder="Phone" />
                        {{-- <span class="text-danger fs-3">*</span> --}}
                    </div>
                    <span id="phone-error" class="text-danger fs-5 fw-medium"></span>
                </div>
                <div class="pb-3">
                    <div class="d-flex align-items-center gap-1">
                        <input type="text" id="address" name="address"
                            class="px-3 py-2 fs-5 form-control  custom-input  input" placeholder="Address" />
                    </div>
                    <span id="address-error" class="text-danger fs-5 fw-medium"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger add-customer-modal-close-btn">Close</button>
                <button type="button" id="customer-submit-btn" class="btn btn-success">
                        <span class="fw-semibold">Save</span>
                        <div id="customer-btn-spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="add-customer-modal-backdrop"></div>
