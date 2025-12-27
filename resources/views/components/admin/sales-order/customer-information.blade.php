<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-user me-2 text-primary"></i>Customer Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="customer" class="form-label">Customer</label>
                <select class="form-select" id="customer" required onchange="loadCustomerInfo()">
                    <option value="">Select Customer</option>
                    <option value="acme">Acme Corporation</option>
                    <option value="techsoft">TechSoft Solutions</option>
                    <option value="global">Global Industries</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="customerEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="customerEmail" placeholder="customer@example.com">
            </div>
            <div class="col-12 mb-3">
                <label for="billingAddress" class="form-label">Billing Address</label>
                <textarea class="form-control" id="billingAddress" rows="3"
                    placeholder="Enter billing address"></textarea>
            </div>
            <div class="col-12 mb-3">
                <label for="shippingAddress" class="form-label">Shipping Address</label>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="sameAsBilling" onchange="copyBillingAddress()">
                    <label class="form-check-label" for="sameAsBilling">
                        Same as billing address
                    </label>
                </div>
                <textarea class="form-control" id="shippingAddress" rows="3"
                    placeholder="Enter shipping address"></textarea>
            </div>
        </div>
    </div>
</div>
