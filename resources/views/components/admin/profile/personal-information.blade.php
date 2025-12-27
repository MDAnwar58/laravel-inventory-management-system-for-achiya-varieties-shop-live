<div class="col-md-8 col-xl-9">
  <div class="card">
    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Basic Information</h5>
      <button type="button" id="edit-btn" class="btn btn-sm btn-primary rounded-3">
        <i class="align-middle" data-feather="edit" style="width: 17px; height: 17px;"></i>
      </button>
    </div>
    <div class="card-body h-100">
      <div class="pb-3 d-flex gap-1">
        <input type="text" name="name" class="px-3 py-2 form-control focus-ring-none input" placeholder="Name"
          value="{{ auth()->user()->name ?? '' }}" disabled />
        <span class="text-danger fw-bold">*</span>
        <x-error fieldName="name" />
      </div>
      <div class="pb-3 d-flex gap-1">
        <input type="email" name="email" class="px-3 py-2 form-control focus-ring-none" placeholder="Email"
          value="{{ auth()->user()->email ?? '' }}" disabled />
        <span class="text-danger fw-bold">*</span>
        <x-error fieldName="email" />
      </div>
      <div>
        <div class="d-flex gap-1">
          <div class="input-group">
            <span class="input-group-text text-muted" id="basic-addon1">+880</span>
            <input type="text" name="phone_number" class="form-control focus-ring-none py-2 input"
              placeholder="Phone number" value="{{ auth()->user()->phone_number ?? '' }}" disabled />
          </div><span class="text-danger fw-bold">*</span>
        </div>
        <x-error fieldName="phone_number" />
      </div>
      <hr />
      <h5 class="text-muted fs-5 fw-semibold mb-3">Personal Information</h5>
      <div class="row">
        <div class="col-lg-9 col-md-8 pb-3">
          <input type="text" name="city" class="px-3 py-2 form-control focus-ring-none input" placeholder="City"
            value="{{ auth()->user()->profile->city ?? '' }}" disabled />
          <x-error fieldName="city" />
        </div>
        <div class="col-lg-3 col-md-4 pb-3">
          <div class="d-flex gap-1">
            <input type="text" name="zip_code" class="px-3 py-2 form-control focus-ring-none input"
              placeholder="Zip code" value="{{ auth()->user()->profile->zip_code ?? '' }}" disabled />
            <span class="text-danger fw-bold">*</span>
          </div>

          <x-error fieldName="zip_code" />
        </div>
      </div>

      <div class="pb-3">
        <div class="d-flex gap-1">
          <input type="text" name="present_address" class="px-3 py-2 form-control focus-ring-none input"
            placeholder="Preset address" value="{{ auth()->user()->profile->present_address ?? '' }}" disabled />
          <span class="text-danger fw-bold">*</span>
        </div>
        <x-error fieldName="present_address" />
      </div>
      <div>
        <input type="text" name="address" class="px-3 py-2 form-control focus-ring-none input" placeholder="Address"
          value="{{ auth()->user()->profile->address ?? '' }}" disabled />
        <x-error fieldName="address" />
      </div>
      <hr />
      <h5 class="text-muted fs-5 fw-semibold mb-3">National ID/Smart Card</h5>
      <div class="row">
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-6">
          <label for="front_side">Front Side</label><span class="text-warning fw-bold">*</span>
          <div class="position-relative">
            <button type="button" id="front-side-remover"
              class="{{ !empty(auth()->user()->profile?->card_front_side) ? 'disabled' : 'd-none' }} btn btn-sm btn-outline-danger rounded-3 position-absolute top-0 end-0 mt-3 me-3"
              style="z-index: 10000;"><i class="feather-sm" data-feather="x"
                style="width: 19px; height: 19px;"></i></button>
            <!-- @if (!empty(auth()->user()->profile?->card_front_side))
              <button type="button" id="delete-front-side-img"
                class="btn btn-sm btn-outline-danger rounded-3 position-absolute top-0 end-0 mt-3 me-3"
                style="z-index: 10000;"><i class="feather-sm" data-feather="x"
                  style="width: 19px; height: 19px;"></i></button>
            @endif -->
            <div id="front-side-uploader"
              class="card uploader border-2 border-secondary-subtle shadow-lg rounded-5 d-flex align-items-center justify-content-center mt-2"
              style="border: dashed;">
              <div id="img-preview-front"
                class="w-100 h-100 {{ empty(auth()->user()->profile?->card_front_side) ? 'd-none' : '' }}">
                <img class="img-fluid rounded-5 img-prev w-100" src="{{ auth()->user()->profile?->card_front_side }}"
                  alt="Front Side" />
              </div>
              <div id="front-side-content"
                class="text-center {{ !empty(auth()->user()->profile?->card_front_side) ? 'd-none' : '' }}">
                <i class="align-middle me-2 text-muted" data-feather="share" style="height: 2.5em;width: 2.5em;"></i>
                <p class="pt-2 text-muted">
                  File upload minimum 20mb <br /> and types jpg,jpeg,png.
                </p>
              </div>
            </div>
          </div>
          <input type="file" id="front-side" name="card_front_side" class="d-none input" disabled />
        </div>
        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-6">
          <label for="back_side">Back Side</label><span class="text-warning fw-bold">*</span>
          <div class="position-relative">
            <button type="button" id="back-side-remover"
              class="{{ !empty(auth()->user()->profile?->card_back_side) ? 'disabled' : 'd-none' }} btn btn-sm btn-outline-danger rounded-3 position-absolute top-0 end-0 mt-3 me-3"
              style="z-index: 10000;"><i class="feather-sm" data-feather="x"
                style="width: 19px; height: 19px;"></i></button>
            <!-- @if (!empty(auth()->user()->profile->card_back_side))
              <button type="button" id="delete-back-side-img"
                class="btn btn-sm btn-outline-danger rounded-3 position-absolute top-0 end-0 mt-3 me-3"
                style="z-index: 10000;"><i class="feather-sm" data-feather="x"
                  style="width: 19px; height: 19px;"></i></button>
            @endif -->
            <div id="back-side-uploader"
              class="card uploader border-2 border-secondary-subtle shadow-lg rounded-5 d-flex align-items-center justify-content-center mt-2 z-0"
              style="border: dashed;">
              <div id="img-preview-back" alt="Back Side"
                class="w-100 h-100 {{ empty(auth()->user()->profile?->card_back_side) ? 'd-none' : '' }}">
                <img class="img-fluid rounded-5 img-prev w-100" src="{{ auth()->user()->profile?->card_back_side }}"
                  alt="back side" />
              </div>
              <div id="back-side-content"
                class="text-center {{ !empty(auth()->user()->profile?->card_back_side) ? 'd-none' : '' }}">
                <i class="align-middle me-2 text-muted" data-feather="share" style="height: 2.5em;width: 2.5em;"></i>
                <p class="pt-2 text-muted">
                  File upload minimum 20mb <br /> and types jpg,jpeg,png.
                </p>
              </div>
            </div>
          </div>
          <input type="file" id="back-side" name="card_back_side" class="d-none input" disabled />
        </div>
      </div>
      <div class="d-flex justify-content-end">
        <button type="submit" class="d-flex gap-1 align-items-center btn btn-primary fs-4 button submit-btn d-flex"
          disabled>
          <span>Save & Change</span>

          <div id="spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </button>
      </div>
    </div>
  </div>
</div>
