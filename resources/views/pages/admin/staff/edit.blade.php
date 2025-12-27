@extends('layouts.admin-layout')

@section('title', '- User Edit')

@push('style')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
    integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('content')
  <x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />
  <div class="row">
    <div class="col-md-12">
      <div class="card shadow-sm">
        <div class="card-header pb-0">
          <h5 class="card-title mb-0 fs-4">User Edit</h5>
        </div>
        <form action="{{ route('admin.user.update', $staff->id) }}" method="POST" enctype="multipart/form-data"
          class="card-body">
          @csrf
          <div class="pb-3">
            <input type="text" name="name" class="px-3 py-2 fs-5 form-control focus-ring-none input" placeholder="Name"
              value="{{ $staff->name }}" />
            <x-error fieldName="name" />
          </div>
          <div class="pb-3">
            <div class="input-group">
              <input type="email" id="email" name="email" class="px-3 py-2 fs-5 form-control focus-ring-none"
                placeholder="Email" value="{{ $staff->email }}" disabled />
              <button type="button" id="email-edit-btn" class="btn btn-sm btn-outline-primary rounded-end-3">
                <i id="email-edit-icon" class="fa-solid fa-pen-to-square fs-4"></i>
                <i id="email-disable-icon" class="fa-solid fa-ban d-none fs-4"></i>
              </button>
            </div>
            <x-error fieldName="email" />
          </div>
          <div class="pb-3">
            <div class="input-group">
              <span class="input-group-text text-muted fs-5" id="basic-addon1">+880</span>
              <input type="text" id="phone-number" name="phone_number"
                class="form-control focus-ring-none fs-5 py-2 input" placeholder="Phone number"
                value="{{ $staff->phone_number }}" disabled />
              <button type="button" id="phone-number-edit-btn" class="btn btn-sm btn-outline-primary rounded-end-3">
                <i id="phone-number-edit-icon" class="fa-solid fa-pen-to-square fs-4"></i>
                <i id="phone-number-disable-icon" class="fa-solid fa-ban d-none fs-4"></i>
              </button>
            </div>
            <x-error fieldName="phone_number" />
          </div>
          <div class="pb-3">
            <div class="d-flex align-items-center gap-1">
              <div class="input-group">
                <span class="input-group-text text-muted fs-5" id="basic-addon1"><i class="fa-solid fa-bangladeshi-taka-sign text-muted" style="font-size: .9rem;"></i></span>
                <input type="text" name="salary" class="form-control focus-ring-none fs-5 py-2 input"
                  placeholder="Salary" value="{{ $staff->salary }}" />
              </div>
            </div>
            <x-error fieldName="salary" />
          </div>

          <div class="pb-3">
            <div class="row">
              <div class="col-xl-9 col-lg-8 col-md-7">
                <input type="text" name="city" class="px-3 py-2 fs-5 form-control focus-ring-none" placeholder="City"
                  value="{{ $staff->profile?->city }}" />
                <x-error fieldName="city" />
              </div>
              <div class="col-xl-3 col-lg-4 col-md-5">
                <input type="number" name="zip_code" class="px-3 py-2 fs-5 form-control focus-ring-none"
                  placeholder="Zip code" value="{{ $staff->profile?->zip_code }}" />
                <x-error fieldName="zip_code" />
              </div>
            </div>
          </div>
          <div class="pb-3">
            <input type="text" name="present_address" class="px-3 py-2 fs-5 form-control focus-ring-none"
              placeholder="Preset address" value="{{ $staff->profile?->present_address }}" />
            <x-error fieldName="present_address" />
          </div>
          <div class="pb-3">
            <input type="text" name="address" class="px-3 py-2 fs-5 form-control focus-ring-none" placeholder="Address"
              value="{{ $staff->profile?->address }}" />
            <x-error fieldName="address" />
          </div>
          <div class="pb-3">
            <select name="role" class="px-3 py-2 fs-5 form-control focus-ring-none">
              <option value="super_admin" {{ $staff->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
              <option value="admin" {{ $staff->role == 'admin' ? 'selected' : '' }}>Admin</option>
              <option value="manager" {{ $staff->role == 'manager' ? 'selected' : '' }}>Manager</option>
              <option value="staff" {{ $staff->role == 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
            <x-error fieldName="role" />
          </div>
          <div class="pb-3">
            <select name="is_active" class="px-3 py-2 fs-5 form-control focus-ring-none">
              <option value="1" {{ $staff->is_active == true ? 'selected' : '' }}>Active</option>
              <option value="0" {{ $staff->is_active == false ? 'selected' : '' }}>Deactive</option>
            </select>
            <x-error fieldName="is_active" />
          </div>
          <div class="pb-3">
            <div id="avater-prev-div" class="d-flex {{ $staff->avatar ? 'pb-3' : '' }}">
              <div class="position-relative">
                <button type="button" id="remove-btn"
                  class="btn btn-sm btn-outline-danger rounded-3 position-absolute top-0 end-0 me-3 mt-2 {{ $staff->avatar ? '' : 'd-none' }}"><i
                    class="feather-sm" data-feather="x"></i></button>
                <img id="avater-prev" src="{{ $staff->avatar }}"
                  class="rounded-circle {{ $staff->avatar ? '' : 'd-none' }}" alt="Avatar"
                  style="width: 200px; height: 200px;" />
              </div>
            </div>
            <input type="file" id="avatar" name="avatar" class="px-3 py-2 fs-5 form-control focus-ring-none" />
            <x-error fieldName="avatar" />
          </div>
          <div class="text-end">
            <button type="submit" class="submit-btn btn btn-primary fs-4 px-3">
              <span class="fw-semibold">Update</span>
              <div id="spinner-border" class="spinner-border spinner-border-sm d-none" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('script')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const submitBtn = document.querySelector('.submit-btn')
      const spinner = document.getElementById('spinner-border')
      const avatarInput = document.getElementById('avatar')
      const avatarPrev = document.getElementById('avater-prev')
      const removeBtn = document.getElementById('remove-btn')
      const avatarPrevDiv = document.getElementById('avater-prev-div')
      const emailEditBtn = document.getElementById('email-edit-btn')
      const emailInput = document.getElementById('email')
      const emailEditIcon = document.getElementById('email-edit-icon')
      const emailDisableIcon = document.getElementById('email-disable-icon')
      const phoneNumberEditBtn = document.getElementById('phone-number-edit-btn')
      const phoneNumberInput = document.getElementById('phone-number')
      const phoneNumberEditIcon = document.getElementById('phone-number-edit-icon')
      const phoneNumberDisableIcon = document.getElementById('phone-number-disable-icon')
      avatarInput.value = ''
      emailInput.disabled = true
      phoneNumberInput.disabled = true


      avatarInput.addEventListener('change', function (e) {
        const file = e.target.files[0]
        const reader = new FileReader()
        reader.onload = function (e) {
          removeBtn.classList.remove('d-none')
          avatarPrev.classList.remove('d-none')
          avatarPrev.src = e.target.result
          avatarPrevDiv.classList.add('pb-3')
        }
        reader.readAsDataURL(file)
      })

      removeBtn.addEventListener('click', function () {
        avatarPrev.classList.add('d-none')
        removeBtn.classList.add('d-none')
        avatarPrevDiv.classList.remove('pb-3')
        avatarInput.value = ''
      })

      emailEditBtn.addEventListener('click', function () {
        if (emailInput.disabled === true) {
          emailInput.disabled = false
          emailEditIcon.classList.add('d-none')
          emailDisableIcon.classList.remove('d-none')
          this.classList.remove('btn-outline-primary')
          this.classList.add('btn-outline-danger')
        } else {
          emailInput.disabled = true
          emailEditIcon.classList.remove('d-none')
          emailDisableIcon.classList.add('d-none')
          this.classList.remove('btn-outline-danger')
          this.classList.add('btn-outline-primary')
        }
      })
      phoneNumberEditBtn.addEventListener('click', function () {
        if (phoneNumberInput.disabled === true) {
          phoneNumberInput.disabled = false
          phoneNumberEditIcon.classList.add('d-none')
          phoneNumberDisableIcon.classList.remove('d-none')
          this.classList.remove('btn-outline-primary')
          this.classList.add('btn-outline-danger')
        } else {
          phoneNumberInput.disabled = true
          phoneNumberEditIcon.classList.remove('d-none')
          phoneNumberDisableIcon.classList.add('d-none')
          this.classList.remove('btn-outline-danger')
          this.classList.add('btn-outline-primary')
        }
      })

      submitBtn.addEventListener('click', function () {
        spinner.classList.remove('d-none')
        submitBtn.classList.add('opacity-50')
      })
    })
  </script>
@endpush
