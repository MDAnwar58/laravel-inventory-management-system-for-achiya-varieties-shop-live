@extends('layouts.admin-layout')
@section('title', '- Settings')

@push('style')
<x-admin.settings.styles />
@endpush

@section('content')
<x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-body px-0">
                @if(in_array(auth()->user()->role, ['owner', 'admin', 'super_admin']))
                <x-admin.settings.authentication-input />
                @endif
                <x-admin.settings.low-stock-alert-input :lStockAlert="$lStockAlert" />

                @if(in_array(auth()->user()->role, ['owner', 'admin', 'super_admin']))
                <x-admin.settings.delete-option />
                <x-admin.settings.low-stock-alert-message-input />
                <x-admin.settings.low-stock-alert-times-input />
                @endif
                <x-admin.settings.domain-name :domain="$setting->domain_name" />
                @if(in_array(auth()->user()->role, ['owner', 'admin', 'super_admin']))
                <x-admin.settings.domain-registration-date :domain_registration_date="$setting->domain_registration_date" />
                <x-admin.settings.domain-renewal-date :domain_renewal_date="$setting->domain_renewal_date" />
                @endif
            </div>
        </div>
    </div>
</div>

<x-admin.settings.alert-msg-modal />
<x-admin.settings.times-modal :setting="$setting" />
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
@if (Session::has('status'))
<x-alert :msg="Session::get('msg')" :status="Session::get('status')" />
@endif
<x-admin.settings.scripts :setting="$setting" :lStockAlert="$lStockAlert" />
@endpush
