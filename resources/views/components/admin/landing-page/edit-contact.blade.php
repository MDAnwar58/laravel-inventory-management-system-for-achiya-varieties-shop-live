<div class="modal fade" id="contactEditModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form id="edit-contact-form" class="modal-dialog  modal-dialog-centered">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold text-secondary" id="staticBackdropLabel">Edit Alert Message</h1>
                <button type="button" class="btn-close edit-contact-modal-close-btn"></button>
            </div>
            <div class="modal-body">
                <div class="pb-3">
                    <input type="text" name="title" id="edit-title" class="form-control" placeholder="Title" />
                    @error('title')
                    <span class="text-danger fs-5 fw-medium">{{ $message }}</span>
                    @enderror
                </div>
                <div class="pb-3">
                    <select name="type" id="edit-type" class="form-select">
                        <option value="">Select Type</option>
                        <option value="address">Address</option>
                        <option value="phone">Phone</option>
                        <option value="email">Email</option>
                        <option value="business_hour">Business Hour</option>
                    </select>
                    @error('type')
                    <span class="text-danger fs-5 fw-medium">{{ $message }}</span>
                    @enderror
                </div>
                <div class="pb-3">
                    <textarea name="content" id="edit-contact-content" class="px-3 py-2 fs-5 form-control text-dark" placeholder="Low Stock Alert Message"></textarea>
                    @error('content')
                    <span class="text-danger fs-5 fw-medium">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger edit-contact-modal-close-btn">Close</button>
                <button type="submit" id="edit-contact-submit-btn" class="btn btn-success">
                    <span class="fw-semibold">Update</span>
                    <div id="edit-contact-spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            </div>
        </div>
    </form>
</div>

<div class="edit-contact-modal-backdrop"></div>
