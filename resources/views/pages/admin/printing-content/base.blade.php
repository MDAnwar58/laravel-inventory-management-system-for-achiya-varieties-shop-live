@extends('layouts.admin-layout')
@section('title', '- Printing Content')

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
    <x-admin.printing-content.form :printing_content="$printing_content" />
  </div>

@endsection

@push('script')
   @if (Session::has('status'))
    <x-alert :msg="Session::get('msg')" :status="Session::get('status')" />
  @endif
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const printingContentSubmitBtn = document.querySelector('.printing-content-submit-btn')
      const printingContentSpinnerBorder = document.getElementById('printing-content-spinner-border')
      
      printingContentSubmitBtn.addEventListener('click', function () {
        printingContentSpinnerBorder.classList.remove('d-none')
        printingContentSubmitBtn.classList.add('disabled')
      })
    })
  </script>
@endpush
