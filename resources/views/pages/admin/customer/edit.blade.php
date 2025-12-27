@extends('layouts.admin-layout')
@section('title', '- Customer Edit')



@section('content')
  <x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />
  <div class="row">
    <div class="col-md-12">
      <div class="card shadow-sm">
        <div class="card-header pb-0">
          <h5 class="card-title mb-0 fs-4">Customer Edit</h5>
        </div>
        <form action="{{ route('admin.customer.update', $data->id) }}" method="POST" enctype="multipart/form-data"
          class="card-body">
          @csrf
          <div class="pb-3">
            <div class="d-flex align-items-center gap-1">
              <input type="text" id="name" name="name" class="px-3 py-2 fs-5 form-control  custom-input  input"
                value="{{ $data->name }}" placeholder="Name" />
              <span class="text-danger fs-3">*</span>
            </div>
            <x-error fieldName="name" />
          </div>
          <div class="pb-3">
            <div class="d-flex align-items-center gap-2">
              <input type="text" id="phone" name="phone" class="px-3 py-2 fs-5 form-control  custom-input  input"
                placeholder="Phone" value="{{ $data->phone }}" {{ $data->phone ? 'disabled' : '' }} />
              <div class="form-check form-switch">
                <input class="form-check-input" id="edit-phone-switch" type="checkbox" role="switch">
              </div>
            </div>
            <x-error fieldName="phone" />
          </div>
          <div class="pb-3">
            <div class="d-flex align-items-center gap-1">
              <input type="text" id="address" name="address" class="px-3 py-2 fs-5 form-control  custom-input  input"
                placeholder="Address" value="{{ $data->address }}" />
              <!-- <span class="text-danger fs-3">*</span> -->
            </div>
            <x-error fieldName="address" />
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
      const nameInput = document.getElementById('name')
      const phoneInput = document.getElementById('phone')
      const addressInput = document.getElementById('address')
      const editPhoneSwitch = document.getElementById('edit-phone-switch')
      const name = '{{ $data->name }}'
      const phone = '{{ $data->phone }}'
      if(!phone)editPhoneSwitch.checked = true
      else editPhoneSwitch.checked = false
      if (!name) nameInput.value = ''
      if (!phoneInput.value) phoneInput.value = ''
      if (!addressInput.value) addressInput.value = ''

      editPhoneSwitch.addEventListener('change', function () {
        if (editPhoneSwitch.checked) {
          phoneInput.disabled = false
        } else {
          phoneInput.disabled = true
        }
      })
      submitBtn.addEventListener('click', function () {
        spinner.classList.remove('d-none')
        submitBtn.classList.add('disabled')
      })
    })
  </script>
@endpush
