<div class="modal fade" id="alertMsgAddModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold text-secondary" id="staticBackdropLabel">Add Alert Message</h1>
                <button type="button" class="btn-close alert-msg-modal-close-btn"></button>
            </div>
            <div class="modal-body">
                <div class="pb-3">
                    <textarea name="low_stock_alert_msg" id="low-stock-alert-msg-textarea" class="px-3 py-2 fs-5 form-control" placeholder="Low Stock Alert Message"></textarea>
                    <span id="low-stock-alert-msg-error" class="text-danger fs-5 fw-medium"></span>
                    <div id="count-parent" class="text-end">
                        <span id="count">0</span>/<span>300</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger alert-msg-modal-close-btn">Close</button>
                <button type="button" id="alert-msg-submit-btn" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="add-alert-msg-modal-backdrop"></div>
