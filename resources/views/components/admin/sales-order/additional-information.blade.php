<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-sticky-note me-2 text-primary"></i>Additional Information
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="paymentTerms" class="form-label">Payment Terms</label>
                <select class="form-select" id="paymentTerms">
                    <option value="net30">Net 30</option>
                    <option value="net15">Net 15</option>
                    <option value="cod">Cash on Delivery</option>
                    <option value="advance">Advance Payment</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="priority" class="form-label">Priority</label>
                <select class="form-select" id="priority">
                    <option value="normal">Normal</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div class="col-12 mb-3">
                <label for="notes" class="form-label">Internal Notes</label>
                <textarea class="form-control" id="notes" rows="3"
                    placeholder="Add any internal notes here..."></textarea>
            </div>
            <div class="col-12">
                <label for="customerNotes" class="form-label">Customer Notes</label>
                <textarea class="form-control" id="customerNotes" rows="3"
                    placeholder="Notes visible to customer..."></textarea>
            </div>
        </div>
    </div>
</div>
