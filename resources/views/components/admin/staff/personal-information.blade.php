@props(['staff' => null])
<div class="col-md-8 col-xl-9">
  <div class="card">
    <div class="card-body h-100">
      <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="pills-overview-tab" data-bs-toggle="pill" data-bs-target="#pills-overview"
            type="button" role="tab" aria-controls="pills-overview" aria-selected="true">Overview</button>
        </li>
        @if(!in_array($staff->role, ['owner', 'admin', 'super_admin']))
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="pills-documents-tab" data-bs-toggle="pill" data-bs-target="#pills-documents"
            type="button" role="tab" aria-controls="pills-documents" aria-selected="false">Documents</button>
        </li>
        @endif
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-overview" role="tabpanel" aria-labelledby="pills-overview-tab"
          tabindex="0">
          <div class="p-1"></div>
          <h5 class="card-title text-secondary fs-4" style="margin-bottom: .65rem;">Basic Information</h5>

          <p class="mt-1 fs-5 fw-medium"><strong>Name:</strong> {{ $staff?->name }}</p>
          <hr style="margin: -0.41rem 0 0.7rem 0;" />
          <p class="mt-1 fs-5 fw-medium"><strong>Email:</strong> {{ $staff?->email }}</p>
          <hr style="margin: -0.41rem 0 0.7rem 0;" />
          <p class="mt-1 fs-5 fw-medium"><strong>Phone Number:</strong> {{ $staff?->phone_number }}</p>

          <h5 class="card-title text-secondary fs-4 mt-3" style="margin-bottom: .65rem;">Personal Information</h5>
          <p class="mt-1 fs-5 fw-medium">
            <strong>City:</strong> {{ $staff?->profile?->city }}
          </p>
          <hr style="margin: -0.41rem 0 0.7rem 0;" />
          <p class="mt-1 fs-5 fw-medium">
            <strong>Zip Code:</strong> {{ $staff?->profile?->zip_code }}
          </p>
          <hr style="margin: -0.41rem 0 0.7rem 0;" />
          <p class="mt-1 fs-5 fw-medium">
            <strong>Present Address:</strong> {{ $staff?->profile?->present_address }}
          </p>
          <hr style="margin: -0.41rem 0 0.7rem 0;" />
          <p class="mt-1 fs-5 fw-medium">
            <strong>Address:</strong> {{ $staff?->profile?->address }}
          </p>
        </div>
        <div class="tab-pane fade" id="pills-documents" role="tabpanel" aria-labelledby="pills-documents-tab"
          tabindex="0">
          <div class="p-1"></div>
          <h5 class="card-title" style="margin-bottom: .65rem;">National ID/Smart Card</h5>
          @if (!empty($staff->profile?->card_front_side) || !empty($staff->profile?->card_back_side))
            <div class="row">
              <div
                class="col-xl-6 col-lg-12 col-md-12 col-sm-6 {{ empty($staff->profile?->card_front_side) ? 'd-none' : '' }}">
                <label for="front_side">Front Side</label><span class="text-warning fw-bold">*</span>
                <div class="position-relative">
                  <div id="front-side-btn"
                    class="card uploader border-2 border-secondary-subtle shadow-lg rounded-5 d-flex align-items-center justify-content-center mt-2"
                    style="border: dashed;" data-bs-toggle="modal" data-bs-target="#documentModal">
                    <div id="img-preview-front" class="w-100 h-100">
                      <img class="img-fluid rounded-5 img-prev w-100" src="{{ $staff->profile?->card_front_side }}"
                        alt="Front Side" />
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="col-xl-6 col-lg-12 col-md-12 col-sm-6 {{ empty($staff->profile?->card_back_side) ? 'd-none' : '' }}">
                <label for="back_side">Back Side</label><span class="text-warning fw-bold">*</span>
                <div class="position-relative">
                  <div id="back-side-btn"
                    class="card uploader border-2 border-secondary-subtle shadow-lg rounded-5 d-flex align-items-center justify-content-center mt-2 z-0"
                    style="border: dashed;" data-bs-toggle="modal" data-bs-target="#documentModal">
                    <div id="img-preview-back" alt="Back Side" class="w-100 h-100">
                      <img class="img-fluid rounded-5 img-prev w-100" src="{{ $staff->profile?->card_back_side }}"
                        alt="back side" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @else
            <p class="text-center text-muted fs-5 fw-semibold">No documents uploaded yet.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
