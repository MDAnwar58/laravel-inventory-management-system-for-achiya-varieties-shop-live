@extends('layouts.admin-layout')
@section('title', '- Brand Create')



@section('content')
  <x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />
  <div class="row">
    <div class="col-md-12">
      <div class="card shadow-sm">
        <div class="card-header pb-0">
          <h5 class="card-title mb-0 fs-4">Brand Added</h5>
        </div>
        <form action="{{ route('admin.brand.store') }}" method="POST" enctype="multipart/form-data" class="card-body">
          @csrf
          <div class="pb-3">
            <div class="d-flex align-items-center gap-1">
              <input type="text" name="name" class="px-3 py-2 fs-5 form-control  custom-input  input"
                placeholder="Name" />
              <span class="text-danger fs-3">*</span>
            </div>
            <x-error fieldName="name" />
          </div>
          <div class="pb-3">
            <select name="status" class="px-3 py-2 fs-5 form-select custom-select focus-ring-none">
              <option value="active">Active</option>
              <option value="deactive">Deactive</option>
            </select>
            <x-error fieldName="status" />
          </div>
          <div class="pb-3">
            <div id="image-prev-div" class="d-flex">
              <div class="position-relative">
                <button type="button" id="remove-btn"
                  class="btn btn-sm btn-outline-danger rounded-3 position-absolute top-0 end-0 me-3 mt-2 d-none"><i
                    class="feather-sm" data-feather="x"></i></button>
                <img id="image-prev" class="rounded-circle d-none" alt="Image" style="width: 151px; height: 151px;" />
              </div>
            </div>
            <input type="file" id="image" name="image" class="px-3 py-2 fs-5 form-control focus-ring-none" />
            <x-error fieldName="image" />
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
      const imageInput = document.getElementById('image')
      const imagePrev = document.getElementById('image-prev')
      const removeBtn = document.getElementById('remove-btn')
      const imagePrevDiv = document.getElementById('image-prev-div')
      imageInput.value = ''

      imageInput.addEventListener('change', function (e) {
        const file = e.target.files[0]
        const reader = new FileReader()
        reader.onload = function (e) {
          removeBtn.classList.remove('d-none')
          imagePrev.classList.remove('d-none')
          imagePrev.src = e.target.result
          imagePrevDiv.classList.add('pb-3')
        }
        reader.readAsDataURL(file)
      })

      removeBtn.addEventListener('click', function () {
        imagePrev.classList.add('d-none')
        removeBtn.classList.add('d-none')
        imagePrevDiv.classList.remove('pb-3')
        imageInput.value = ''
      })

      submitBtn.addEventListener('click', function () {
        spinner.classList.remove('d-none')
        submitBtn.classList.add('disabled')
      })
    })
  </script>
@endpush
