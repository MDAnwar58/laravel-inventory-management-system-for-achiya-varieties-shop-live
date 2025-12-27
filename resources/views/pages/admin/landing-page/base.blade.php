@extends('layouts.admin-layout')
@section('title', '- Landing Page')

@push('style')
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
  <style>

    .add-contact-modal-backdrop {
        --bs-backdrop-zindex: 1050;
        --bs-backdrop-bg: rgb(0, 0, 0, .5);
        position: fixed;
        top: 0;
        left: 0;
        z-index: var(--bs-backdrop-zindex);
        width: 100vw;
        backdrop-filter: blur(9px);
        height: 100vh;
        background-color: var(--bs-backdrop-bg);
        opacity: 0;
        visibility: hidden;
        transition: opacity .4s ease-in-out, visibility .4s ease-in-out;
    }

    .add-contact-modal-backdrop.show {
        opacity: 1;
        visibility: visible;
    }
    .edit-contact-modal-backdrop {
        --bs-backdrop-zindex: 1050;
        --bs-backdrop-bg: rgb(0, 0, 0, .5);
        position: fixed;
        top: 0;
        left: 0;
        z-index: var(--bs-backdrop-zindex);
        width: 100vw;
        backdrop-filter: blur(9px);
        height: 100vh;
        background-color: var(--bs-backdrop-bg);
        opacity: 0;
        visibility: hidden;
        transition: opacity .4s ease-in-out, visibility .4s ease-in-out;
    }
    .edit-contact-modal-backdrop.show {
        opacity: 1;
        visibility: visible;
    }
    .add-feature-modal-backdrop {
        --bs-backdrop-zindex: 1050;
        --bs-backdrop-bg: rgb(0, 0, 0, .5);
        position: fixed;
        top: 0;
        left: 0;
        z-index: var(--bs-backdrop-zindex);
        width: 100vw;
        backdrop-filter: blur(9px);
        height: 100vh;
        background-color: var(--bs-backdrop-bg);
        opacity: 0;
        visibility: hidden;
        transition: opacity .4s ease-in-out, visibility .4s ease-in-out;
    }
    .add-feature-modal-backdrop.show {
        opacity: 1;
        visibility: visible;
    }
    .edit-feature-modal-backdrop {
        --bs-backdrop-zindex: 1050;
        --bs-backdrop-bg: rgb(0, 0, 0, .5);
        position: fixed;
        top: 0;
        left: 0;
        z-index: var(--bs-backdrop-zindex);
        width: 100vw;
        backdrop-filter: blur(9px);
        height: 100vh;
        background-color: var(--bs-backdrop-bg);
        opacity: 0;
        visibility: hidden;
        transition: opacity .4s ease-in-out, visibility .4s ease-in-out;
    }
    .edit-feature-modal-backdrop.show {
        opacity: 1;
        visibility: visible;
    }
  </style>
@endpush

@section('content')
  <x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />
  <div class="row">
    <x-admin.landing-page.content-form :landing_page="$landing_page" />
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between pb-0">
          <h5 class="card-title mb-0 fs-4">Features</h5>
          <button type="button" id="add-feature-btn" class="btn btn-sm btn-outline-primary p-1 pb-0 rounded-3"><i class="fa-solid fa-plus fs-5"></i></button>
        </div>
        <div class="card-body pt-0 pb-2">
          <x-admin.landing-page.features-table :features="$features" />
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between pb-0">
          <h5 class="card-title mb-0 fs-4">Contact Informations</h5>
          <button type="button" id="add-contact-info-btn" class="btn btn-sm btn-outline-primary p-1 pb-0 rounded-3"><i class="fa-solid fa-plus fs-5"></i></button>
        </div>
        <div class="card-body pt-0 pb-2">
          <x-admin.landing-page.contact-table :contact_infos="$contact_infos" />
        </div>
      </div>
    </div>
  </div>

  <x-admin.landing-page.add-contact />
  <x-admin.landing-page.edit-contact />
  <x-admin.landing-page.add-feature />
  <x-admin.landing-page.edit-feature />
@endsection

@push('script')
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
   @if (Session::has('status'))
    <x-alert :msg="Session::get('msg')" :status="Session::get('status')" />
  @endif
  <script>
    $(document).ready(function () {
      $('#contact-content').summernote({ height: 200, });
      $('#edit-contact-content').summernote({ height: 200, });
    });
  </script>
  <x-admin.landing-page.scripts />
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const landingPageSubmitBtn = document.querySelector('.landing-page-submit-btn')
      const landingPageSpinnerBorder = document.getElementById('landing-page-spinner-border')
      const editContactSubmitBtn = document.querySelector('#edit-contact-submit-btn')
      const editContactSpinner = document.getElementById('edit-contact-spinner-border')
      const addContactSubmitBtn = document.querySelector('#contact-submit-btn')
      const addContactSpinnerBorder = document.getElementById('add-contact-spinner-border')
      const addFeatureSubmitBtn = document.getElementById('add-feature-submit-btn')
      const addFeatureSpinnerBorder = document.getElementById('add-feature-spinner-border')
      const editFeatureSubmitBtn = document.getElementById("edit-feature-submit-btn")
      const editFeatureSpinnerBorder = document.getElementById("edit-feature-spinner-border")
      

      editContactSubmitBtn.addEventListener('click', function () {
        editContactSpinner.classList.remove('d-none')
        editContactSubmitBtn.classList.add('disabled')
      })

      addContactSubmitBtn.addEventListener('click', function () {
        landingPageSpinnerBorder.classList.remove('d-none')
        addContactSubmitBtn.classList.add('disabled')
      })

      addFeatureSubmitBtn.addEventListener('click', function () {
        addFeatureSpinnerBorder.classList.remove('d-none')
        addFeatureSubmitBtn.classList.add('disabled')
      })

      editFeatureSubmitBtn.addEventListener('click', function () {
        editFeatureSpinnerBorder.classList.remove('d-none')
        editFeatureSubmitBtn.classList.add('disabled')
      })

      landingPageSubmitBtn.addEventListener('click', function () {
        landingPageSpinnerBorder.classList.remove('d-none')
        landingPageSubmitBtn.classList.add('disabled')
      })
    })
  </script>
@endpush
