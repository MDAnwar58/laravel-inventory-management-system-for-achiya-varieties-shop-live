@extends('layouts.admin-layout')
@section('title', '- User Create')

@section('content')
  <x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />
  <div class="row">
    <div class="col-md-12">
      <div class="card shadow-sm">
        <div class="card-header pb-0">
          <h5 class="card-title mb-0 fs-4">User Added</h5>
        </div>
        <form action="{{ route('admin.user.store') }}" method="POST" enctype="multipart/form-data" class="card-body">
          @csrf
          <div class="pb-3">
            <div class="d-flex align-items-center gap-1">
              <input type="text" name="name" class="px-3 py-2 fs-5 form-control focus-ring-none input"
                placeholder="Name" />
              <span class="text-danger fs-3">*</span>
            </div>
            <x-error fieldName="name" />
          </div>
          <div class="pb-3">
            <div class="d-flex align-items-center gap-1">
              <input type="email" name="email" class="px-3 py-2 fs-5 form-control focus-ring-none" placeholder="Email" />
              <span class="text-danger fs-3">*</span>
            </div>
            <x-error fieldName="email" />
          </div>
          <div class="pb-3">
            <div class="d-flex align-items-center gap-1">
              <div class="input-group">
                <span class="input-group-text text-muted fs-5" id="basic-addon1">+880</span>
                <input type="text" name="phone_number" class="form-control focus-ring-none fs-5 py-2 input"
                  placeholder="Phone number" />
              </div>
              <span class="text-danger fs-3">*</span>
            </div>
            <x-error fieldName="phone_number" />
          </div>
          <div class="pb-3">
            <div class="d-flex align-items-center gap-1">
              <div class="input-group">
                <span class="input-group-text text-muted fs-5" id="basic-addon1"><i class="fa-solid fa-bangladeshi-taka-sign text-muted" style="font-size: .9rem;"></i></span>
                <input type="text" name="salary" class="form-control focus-ring-none fs-5 py-2 input"
                  placeholder="Salary" />
              </div>
            </div>
            <x-error fieldName="salary" />
          </div>

          <div class="pb-3">
            <div class="row">
              <div class="col-xl-9 col-lg-8 col-md-7">
                <input type="text" name="city" class="px-3 py-2 fs-5 form-control focus-ring-none" placeholder="City" />
                <x-error fieldName="city" />
              </div>
              <div class="col-xl-3 col-lg-4 col-md-5">
                <div class="d-flex align-items-center gap-1">
                  <input type="number" name="zip_code" class="px-3 py-2 fs-5 form-control focus-ring-none"
                    placeholder="Zip code" />
                  {{-- <span class="text-danger fs-3">*</span> --}}
                </div>
                <x-error fieldName="zip_code" />
              </div>
            </div>
          </div>
          <div class="pb-3">
            <div class="d-flex align-items-center gap-1">
              <input type="text" name="present_address" class="px-3 py-2 fs-5 form-control focus-ring-none"
                placeholder="Preset address" />
              <span class="text-danger fs-3">*</span>
            </div>
            <x-error fieldName="present_address" />
          </div>
          <div class="pb-3">
            <input type="text" name="address" class="px-3 py-2 fs-5 form-control focus-ring-none" placeholder="Address" />
            <x-error fieldName="address" />
          </div>
          <div class="pb-3">
            <select name="role" class="px-3 py-2 fs-5 form-control focus-ring-none">
              <option value="staff">Staff</option>
              <option value="manager">Manager</option>
              <option value="super_admin">Super Admin</option>
              <option value="admin">Admin</option>
            </select>
            <x-error fieldName="role" />
          </div>
          <div class="pb-3">
            <div class="d-flex align-items-center gap-1">
              <input type="password" name="password" class="px-3 py-2 fs-5 form-control focus-ring-none"
                placeholder="Password" />
              <span class="text-danger fs-3">*</span>
            </div>
            <x-error fieldName="password" />
          </div>
          <div class="pb-3">
            <div class="d-flex align-items-center gap-1">
              <input type="password" name="password_confirmation" class="px-3 py-2 fs-5 form-control focus-ring-none"
                placeholder="Confirm Password" />
              <span class="text-danger fs-3">*</span>
            </div>
            <x-error fieldName="password_confirmation" />
          </div>

          <div class="pb-3">
            <div id="avater-prev-div" class="d-flex">
              <div class="position-relative">
                <button type="button" id="remove-btn"
                  class="btn btn-sm btn-outline-danger rounded-3 position-absolute top-0 end-0 me-3 mt-2 d-none"><i
                    class="feather-sm" data-feather="x"></i></button>
                <img id="avater-prev" class="rounded-circle d-none" alt="Avatar" style="width: 300px; height: 300px;" />
              </div>
            </div>
            <input type="file" id="avatar" name="avatar" class="px-3 py-2 fs-5 form-control focus-ring-none" />
            <x-error fieldName="avatar" />
          </div>
          <div class="text-end">
            <button type="submit" class="submit-btn btn btn-primary fs-4 px-3">
              <span class="fw-semibold">Save</span>
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
      avatarInput.value = ''

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

      submitBtn.addEventListener('click', function () {
        spinner.classList.remove('d-none')
        submitBtn.classList.add('opacity-50')
      })
    })
  </script>
@endpush
