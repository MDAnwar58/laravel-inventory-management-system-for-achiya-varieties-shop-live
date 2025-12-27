@props(['staff' => null])
<div class="col-md-4 col-xl-3">
  <div class="card mb-3">
    <div class="card-header">
      <h5 class="card-title mb-0">Profile Details</h5>
    </div>
    <div class="card-body text-center">
      <div class="d-flex justify-content-center">
        <div class="position-relative">
          <button type="button" id="avatar-uploader-btn"
            class="d-none btn btn-sm btn-outline-secondary rounded-3 position-absolute bottom-0 end-0 mb-3 me-2"
            style="z-index: 10000;"><i class="feather-sm" data-feather="camera"></i></button>
          @if ($staff->avatar)
            <img id="avatar-preview" src="{{ $staff->avatar }}" alt="Avatar" class="img-fluid rounded-circle mb-2"
              style="width: 128px; height: 128px;" />
          @else
            <img id="avatar-preview" src="{{ asset('assets/img/avatars/user.png') }}" alt="Avatar"
              class="img-fluid rounded-circle mb-2" style="width: 128px; height: 128px;" />
          @endif

        </div>
      </div>
      <input type="file" id="avatar" name="avatar" class="d-none input" disabled />
      <h5 class="card-title mb-0">{{ $staff->name }}</h5>
      <div id="user-role" class="text-muted mb-2 text-capitalize">User Role</div>

      <!-- {% comment %} <div>
        <a class="btn btn-primary btn-sm" href="#">Follow</a>
        <a class="btn btn-primary btn-sm" href="#"><span data-feather="message-square"></span> Message</a>
      </div> {% endcomment %} -->
    </div>
    <!-- {% comment %} <hr class="my-0" />
    <div class="card-body">
      <h5 class="h6 card-title">Skills</h5>
      <a href="#" class="badge bg-primary me-1 my-1">HTML</a>
      <a href="#" class="badge bg-primary me-1 my-1">JavaScript</a>
      <a href="#" class="badge bg-primary me-1 my-1">Sass</a>
      <a href="#" class="badge bg-primary me-1 my-1">Angular</a>
      <a href="#" class="badge bg-primary me-1 my-1">Vue</a>
      <a href="#" class="badge bg-primary me-1 my-1">React</a>
      <a href="#" class="badge bg-primary me-1 my-1">Redux</a>
      <a href="#" class="badge bg-primary me-1 my-1">UI</a>
      <a href="#" class="badge bg-primary me-1 my-1">UX</a>
    </div> {% endcomment %} -->
    <hr class="my-0" />
    <div class="card-body">
      <h5 class="h6 card-title">About</h5>
      <ul class="list-unstyled mb-0">
        <li class="mb-1">
          <span data-feather="home" class="feather-sm me-1"></span> Lives
          in @if(!empty($staff->profile?->present_address))
            <a>{{ $staff->profile->present_address }}</a>
          @elseif(!empty($staff->profile?->address))
            <a>{{ $staff->profile->address }}</a>
          @else
            <span class="text-muted">Not specified</span>
          @endif
        </li>

        @if(!in_array($staff->role, ['owner', 'admin', 'super_admin']))
          <li class="mb-1">
            <span data-feather="briefcase" class="feather-sm me-1"></span> Works at <a href="#">{{ env('APP_NAME') }}</a>
          </li>
          @if($staff->salary)
            <li class="mb-1">
              <i class="fa-solid fa-money-bill-wave me-1" style="margin-left: -0.2rem;"></i></span> Salary <i class="fa-solid fa-bangladeshi-taka-sign text-muted" style="font-size: .8rem;"></i><a class="text-muted">{{ $staff->salary }}</a>
            </li>
          @endif
        @endif
        <!-- {% comment %} <li class="mb-1">
          <span data-feather="map-pin" class="feather-sm me-1"></span> From <a href="#">Boston</a>
        </li> {% endcomment %} -->
      </ul>
    </div>
  </div>
</div>
