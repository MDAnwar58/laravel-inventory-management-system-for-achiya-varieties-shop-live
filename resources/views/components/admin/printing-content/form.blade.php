@props([
    'printing_content' => null
])
<div class="col-md-12">
    <div class="card shadow-sm">
        <div class="card-header pb-0">
            <h5 class="card-title mb-0 fs-4">Printing Content</h5>
        </div>
        <form action="{{ route('admin.printing.content.store.or.update') }}" method="POST" class="card-body">
            @csrf
            <input type="hidden" name="id" value="{{ $printing_content->id ?? '' }}">
            <div class="pb-3">
                <label for="phone_number" class="pb-1">Phone Number</label>
                <input type="text" name="phone_number" class="px-3 py-2 fs-5 form-control  custom-input  input" value="{{ $printing_content->phone_number ?? '' }}" />
                <x-error fieldName="phone_number" />
            </div>
            <div class="pb-3">
                <label for="phone_number2" class="pb-1">Second Phone Number</label>
                <input type="text" name="phone_number2" class="px-3 py-2 fs-5 form-control custom-input input" value="{{ $printing_content->phone_number2 ?? '' }}" />
                <x-error fieldName="phone_number2" />
            </div>
            <div class="pb-3">
                <label for="location" class="pb-1">Location</label>
                <input type="text" name="location" class="px-3 py-2 fs-5 form-control custom-input input" value="{{ $printing_content->location ?? '' }}" />
                <x-error fieldName="location" />
            </div>
            <div class="pb-3">
                <label for="short_desc" class="pb-1">Short Description</label>
                <textarea name="short_desc" class="px-3 py-2 fs-5 form-control custom-input input" placeholder="write...">{{ $printing_content->short_desc ?? '' }}</textarea>
                <x-error fieldName="short_desc" />
            </div>
            <div class="text-end">
                <button type="submit" class="printing-content-submit-btn btn btn-primary fs-4 px-3"> 
                    <span class="fw-semibold">Save & Change</span>
                    <div id="printing-content-spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>
