<div class="col-lg-12">
    <div class="card mb-4">
        <div class="card-header bg-white pb-0">
            <h5 class="text-secondary fw-bold fs-4 mb-0">
                <i class="fas fa-info-circle fs-3 me-2 text-primary"></i>Order Information
            </h5>
        </div>
        <div class="card-body pt-3">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="order-date" class="form-label fw-medium fs-5 mb-1">Order Date</label>
                    <input type="date" class="form-control fs-5" name="order_date" id="order-date" disabled />
                </div>
                <div class="col-md-6 mb-3">
                    <label for="due_date" class="form-label fw-medium fs-5 mb-1">Due Date</label>
                    <input type="date" class="form-control fs-5" name="due_date" id="due-date" disabled />
                </div>
                <div class="col-md-12 mb-3">
                    <label for="payment_status" class="form-label fw-medium fs-5 mb-1">Payment Status</label>
                    <select class="form-select fs-5" name="payment_status" id="payment_status" disabled>
                        <option value="paid">Paid</option>
                        <option value="due">Full Due</option>
                        <option value="partial due">Partial Due</option>
                    </select>
                </div>
                <div class="col-md-12 mb-3">
                    <label for="memo_no" class="form-label fw-medium fs-5 mb-1">Memo No.</label>
                    <input type="text" class="form-control fs-5" name="memo_no" id="memo_no" disabled />
                </div>
                <div class="col-md-12 mb-3">
                    <label for="notes" class="form-label fw-medium fs-5 mb-1 ">Notes</label>
                    <textarea name="notes" id="notes" class="form-control fs-5" rows="5" placeholder="Write notes..." disabled></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
