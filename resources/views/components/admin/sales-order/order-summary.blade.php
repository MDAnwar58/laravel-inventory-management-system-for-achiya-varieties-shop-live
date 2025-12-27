<div class="col-lg-6">
    <div class="card order-summary mb-4">
        <div class="card-header">
            <h5 class="card-title text-white mb-0">
                <i class="fas fa-calculator me-2"></i>Order Summary
            </h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span>Subtotal:</span>
                <strong><i class="fa-solid fa-bangladeshi-taka-sign"
                        style="font-size: .85rem;margin-right: -0.17rem;"></i><span>0.00</span></strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Tax:</span>
                <strong id="totalTax">$0.00</strong>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span>Shipping:</span>
                <input type="number" class="form-control form-control-sm text-end text-white"
                    style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3);" id="shipping"
                    value="0.00" step="0.01" onchange="calculateTotal()">
            </div>
            <hr class="text-white">
            <div class="d-flex justify-content-between mb-4">
                <h5 class="text-white">Total:</h5>
                <h5 class="text-white">$<span id="grandTotal">0.00</span></h5>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-light btn-lg">
                    <i class="fas fa-check me-1"></i>Create Order
                </button>
                <button type="button" class="btn btn-outline-light" onclick="previewOrder()">
                    <i class="fas fa-eye me-1"></i>Preview
                </button>
            </div>
        </div>
    </div>
</div>
