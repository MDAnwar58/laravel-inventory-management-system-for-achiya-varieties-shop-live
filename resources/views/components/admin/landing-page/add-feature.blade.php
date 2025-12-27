<div class="modal fade" id="featureAddModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <form action="{{ route('admin.landing.page.feature.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold text-secondary" id="staticBackdropLabel">Add Feature</h1>
                <button type="button" class="btn-close feature-modal-close-btn"></button>
            </div>
            <div class="modal-body">
                <div class="pb-3">
                    <input type="text" name="title" class="form-control" placeholder="Title" />
                    @error('title')
                    <span class="text-danger fs-5 fw-medium">{{ $message }}</span>
                    @enderror
                </div>
                <div class="pb-3">
                    <select name="type" class="form-select">
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
                    <textarea name="content" class="px-3 py-2 fs-5 form-control text-dark" placeholder="Low Stock Alert Message"></textarea>
                    @error('content')
                    <span class="text-danger fs-5 fw-medium">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger feature-modal-close-btn">Close</button>
                <button type="submit" id="add-feature-submit-btn" class="btn btn-success">
                    <span class="fw-semibold">Save</span>
                    <div id="add-feature-spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="add-feature-modal-backdrop"></div>
