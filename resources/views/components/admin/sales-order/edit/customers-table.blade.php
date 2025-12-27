<div class="col-xl-6 col-lg-12 mt-lg-0 mt-3 mb-md-0">
    <div class="card">
        <div id="card-title" class=" pt-3 px-4 d-flex flex-sm-row flex-column justify-content-between align-items-center">
            <div class="text-secondary fw-bold fs-4">Customer List</div>
            <div class="d-flex gap-1">
                <input type="text" class="form-control fs-5" id="customer-search" placeholder="Search Products...">
                <button type="button" id="reset-customer-table-btn"
                    class="btn btn-sm btn-light rounded shadow-sm fs-5 fw-semibold"
                    data-bs-toggle="tooltip" data-bs-title="Reset Customers List."
                    >
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
                <button type="button" id="new-customer-add-btn"
                    class="btn btn-sm btn-primary rounded shadow-sm fs-5 fw-semibold" id="openCustomerModalBtn"
                    data-bs-toggle="tooltip" data-bs-title="Add New Customer."
                    >
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        </div>
        <div class=" card-body pt-0">
            <div class="table-responsive">
                <table class="table table-hover" id="customerTable">
                <thead>
                    <tr>
                        <th scope="col" class="text-muted fw-normal">Customer</th>
                        <th scope="col" class="text-muted fw-normal">Pick</th>
                    </tr>
                </thead>
                <tbody id="customerList"></tbody>
            </table>
            </div>
            <x-admin.sales-order.customer-table-footer />
        </div>
    </div>
</div>
