@props([
    'landing_page' => null
])
<div class="col-md-12">
    <div class="card shadow-sm">
        <div class="card-header pb-0">
            <h5 class="card-title mb-0 fs-4">Landing Page Content</h5>
        </div>
        <form action="{{ route('admin.landing.page.store.or.update') }}" method="POST" class="card-body">
            @csrf
            <input type="hidden" name="id" value="{{ $landing_page->id ?? '' }}">
            <div class="pb-3">
                <label for="hero_title_part_1" class="pb-1">Hero Title Part 1</label>
                <input type="text" name="hero_title_part_1" class="px-3 py-2 fs-5 form-control  custom-input  input" value="{{ $landing_page->hero_title_part_1 ?? '' }}" />
                <x-error fieldName="hero_title_part_1" />
            </div>
            <div class="pb-3">
                <label for="hero_title_patt_2" class="pb-1">Hero Title Part 2</label>
                <input type="text" name="hero_title_patt_2" class="px-3 py-2 fs-5 form-control custom-input input" value="{{ $landing_page->hero_title_part_2 ?? '' }}" />
                <x-error fieldName="hero_title_patt_2" />
            </div>
            <div class="pb-3">
                <label for="short_des" class="pb-1">Hero Short Description</label>
                <textarea name="short_des" class="px-3 py-2 fs-5 form-control custom-input input" placeholder="write...">{{ $landing_page->short_des ?? '' }}</textarea>
                <x-error fieldName="short_des" />
            </div>
            <div class="pb-3">
                <label for="features_title" class="pb-1">Features Title Part 1</label>
                <input type="text" name="features_title" class="px-3 py-2 fs-5 form-control custom-input input" value="{{ $landing_page->features_title ?? '' }}" />
                <x-error fieldName="features_title" />
            </div>
            <div class="pb-3">
                <label for="feature_title_part_2" class="pb-1">Features Title Part 2</label>
                <input type="text" name="feature_title_part_2" class="px-3 py-2 fs-5 form-control custom-input input" value="{{ $landing_page->feature_title_part_2 ?? '' }}" />
                <x-error fieldName="feature_title_part_2" />
            </div>
            <div class="pb-3">
                <label for="features_sub_title" class="pb-1">Features Sub Title</label>
                <input type="text" name="features_sub_title" class="px-3 py-2 fs-5 form-control custom-input input" value="{{ $landing_page->features_sub_title ?? '' }}" />
                <x-error fieldName="features_sub_title" />
            </div>
            <div class="pb-3">
                <label for="support_hour" class="pb-1">Services Hours</label>
                <input type="number" name="support_hour" class="px-3 py-2 fs-5 form-control custom-input input" value="{{ $landing_page->support_hour ?? '' }}" />
                <x-error fieldName="support_hour" />
            </div>
            <div class="pb-3">
                <label for="contact_title" class="pb-1">Contact Title</label>
                <input type="text" name="contact_title" class="px-3 py-2 fs-5 form-control custom-input input" value="{{ $landing_page->contact_title ?? '' }}" />
                <x-error fieldName="contact_title" />
            </div>
            <div class="pb-3">
                <label for="contact_sub_title" class="pb-1">Contact Sub Title</label>
                <input type="text" name="contact_sub_title" class="px-3 py-2 fs-5 form-control custom-input input" value="{{ $landing_page->contact_sub_title ?? '' }}" />
                <x-error fieldName="contact_sub_title" />
            </div>
            <div class="text-end">
                <button type="submit" class="landing-page-submit-btn btn btn-primary fs-4 px-3"> 
                    <span class="fw-semibold">Save & Change</span>
                    <div id="landing-page-spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>
