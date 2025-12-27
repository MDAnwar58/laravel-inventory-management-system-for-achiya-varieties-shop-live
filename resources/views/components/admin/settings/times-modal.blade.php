@props([
'setting' => null
])
<div class="modal fade" id="timesAddModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog  modal-sm modal-dialog-centered">
        <form action="{{ route('admin.alert.times.store', $setting->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-semibold text-secondary" id="staticBackdropLabel">Add Times</h1>
                <button type="button" class="btn-close times-modal-close-btn"></button>
            </div>
            <div id="times-input-list" class="modal-body">
                <input type="hidden" name="times" id="times-for-submit">
                <div id="first-time-input" class="pb-3">
                    <input type="time" data-id="" class="form-control fs-4 times-inputs" id="times-input" placeholder="Times">
                </div>
                <button type="button" class="btn btn-primary w-100" id="times-add-btn">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger times-modal-close-btn">Close</button>
                <button type="submit" id="times-submit-btn" class="btn btn-success">Save & Change</button>
            </div>
        </form>
    </div>
</div>

<div class="add-times-modal-backdrop"></div>
