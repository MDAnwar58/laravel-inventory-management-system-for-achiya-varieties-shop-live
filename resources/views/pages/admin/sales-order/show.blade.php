@extends('layouts.admin-layout')
@section('title', '- Sales Order Invoice')

@push('style')
<link rel="stylesheet" href="{{ asset('assets/css/pagination.css') }}">
<style>
    .gradient-app-logo {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .add-customer-modal-backdrop {
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

    .add-customer-modal-backdrop.show {
        opacity: 1;
        visibility: visible;
    }
    .bdt {
        font-family: 'Noto Sans Bengali', sans-serif;
        margin-right: 0.1rem;
    }
</style>
@endpush

@section('content')
<x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />


<div class="row main_row pb-5">
    <x-admin.sales-order.show.billed-card :data="$data" :printing_content="$printing_content" />
</div>

<!-- <x-admin.sales-order.add-new-customer-modal /> -->
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        // console.log('DOMContentLoaded');
        // console.log(window.location.href);
        // console.log(window.location.pathname);
        // console.log(window.location.search);
        // console.log(window.location.hash);
        // console.log(window.location.host);
        // console.log(window.location.hostname);
        // console.log(window.location.port);
    })

</script>
@endpush
