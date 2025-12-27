<div class="modal fade" id="featureEditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <form id="edit-feature-form" class="modal-content">
            @csrf
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold text-secondary" id="staticBackdropLabel">Edit Feature</h1>
                <button type="button" class="btn-close edit-feature-modal-close-btn"></button>
            </div>
            <div class="modal-body">
                <div class="pb-3">
                    <input type="text" name="title" id="edit-feature-title" class="form-control" placeholder="Title" />
                    @error('title')
                    <span class="text-danger fs-5 fw-medium">{{ $message }}</span>
                    @enderror
                </div>
                <div class="pb-3">
                    <select name="type" id="edit-feature-type" class="form-select">
                        <option value="">Select Type</option>
                        <option value="analiytics">Analiytics</option>
                        <option value="real-time-sync">Real Time Sync</option>
                        <option value="receipt-printer">Receipt Printer</option>
                        <option value="advenced-reporting">Advenced Reporting</option>
                        <option value="smart-alert">Smart Alert</option>
                        <option value="inventory-hub">Inventory Hub</option>
                    </select>
                    @error('type')
                    <span class="text-danger fs-5 fw-medium">{{ $message }}</span>
                    @enderror
                </div>
                <div class="pb-3">
                    <textarea name="content" id="edit-feature-content" class="px-3 py-2 fs-5 form-control text-dark" placeholder="Low Stock Alert Message"></textarea>
                    @error('content')
                    <span class="text-danger fs-5 fw-medium">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger edit-feature-modal-close-btn">Close</button>
                <button type="submit" id="edit-feature-submit-btn" class="btn btn-success">
                    <span class="fw-semibold">Update</span>
                    <div id="edit-feature-spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="edit-feature-modal-backdrop"></div>
