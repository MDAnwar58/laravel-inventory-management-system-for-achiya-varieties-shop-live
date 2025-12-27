@extends('layouts.admin-layout')

@section('title', '| Item Type Edit')

@section('content')
  <x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />
  <div class="row">
    <div class="col-md-12">
      <div class="card shadow-sm">
        <div class="card-header pb-0">
          <h5 class="card-title mb-0 fs-4">Item Type Edit</h5>
        </div>
        <form action="{{ route('admin.item.type.update', $data->id) }}" method="POST" enctype="multipart/form-data"
          class="card-body">
          @csrf
          <div class="pb-3">
            <input type="text" name="name" class="px-3 py-2 fs-5 form-control focus-ring-none input" placeholder="Name"
              value="{{ $data->name }}" />
            <x-error fieldName="name" />
          </div>
          <div class="pb-3">
            <select name="status" class="px-3 py-2 fs-5 form-select custom-select focus-ring-none">
              <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>Active</option>
              <option value="deactive" {{ $data->status == 'deactive' ? 'selected' : '' }}>Deactive</option>
            </select>
            <x-error fieldName="status" />
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

      submitBtn.addEventListener('click', function () {
        spinner.classList.remove('d-none')
        submitBtn.classList.add('opacity-50')
      })
    })
  </script>
@endpush
