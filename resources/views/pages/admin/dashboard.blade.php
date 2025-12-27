@extends('layouts.admin-layout')

@section('title', '- Dashboard')

@push('style')
<style>
    .bdt {
        font-family: 'Noto Sans Bengali', sans-serif;
        margin-right: 0.1rem;
    }
    #chartjs-dashboard-pie {
        height: 400px;
        width: 400px;
    }
</style>
@endpush

@section('content')
<h1 class="h3 mb-3"><strong>Analytics</strong> Dashboard</h1>

<div class="row justify-content-center">
    <div class="col-md-12 d-flex">
        <div class="w-100">
            <div class="row">
                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <x-admin.dashboard.sales-units-count-card :salesOrderProductsUnitsCountAndPerchent="$salesOrderProductsUnitsCountAndPerchent" />
                    {{-- <x-admin.dashboard.sales-products-count-card :salesOrderProductsCountAndPerchent="$salesOrderProductsCountAndPerchent" /> --}}
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <x-admin.dashboard.sales-weight-card :salesOrderProductsWeightsCountAndPercent="$salesOrderProductsWeightsCountAndPercent" />
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <x-admin.dashboard.sales-feets-card  :salesOrderProductsFootsCountAndPercent="$salesOrderProductsFootsCountAndPercent" />
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <x-admin.dashboard.sales-yard-card  :salesOrderProductsYardsCountAndPercent="$salesOrderProductsYardsCountAndPercent" />
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <x-admin.dashboard.sales-meter-card :salesOrderProductsMetersCountAndPercent="$salesOrderProductsMetersCountAndPercent" />
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <x-admin.dashboard.orders-card :totalOrdersCountAndMonthlySalesOrdersPercentUpAndDown="$totalOrdersCountAndMonthlySalesOrdersPercentUpAndDown" />
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <x-admin.dashboard.earnings-card :totalSalesOrderEarningsAndMonthlyEarningsIncreaseAndDecreasePercent="$totalSalesOrderEarningsAndMonthlyEarningsIncreaseAndDecreasePercent" />
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <x-admin.dashboard.customers-card :totalCustomersCount="$totalCustomersCount" />
                </div>

                <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-4 col-sm-6">
                    <x-admin.dashboard.total-value-of-products-on-store :totalvalueOfProductsOnStore="$totalvalueOfProductsOnStore" />
                </div>
            </div>
        </div>
    </div>

    <x-admin.dashboard.calendar-card />

    <x-admin.dashboard.monthly-sales />
</div>

<div class="row justify-content-center">
    <x-admin.dashboard.monthly-sales-products-card />
    <x-admin.dashboard.top-5-products-sales-count :salesTop5ProductsCount="$salesTop5ProductsCount" />
</div>

<div class="row">
<x-admin.dashboard.current-week-sales-earnings-and-products-card />
</div>

<div class="row">
    <x-admin.dashboard.recent-sales-orders :latest_orders="$latest_orders" />

    <x-admin.dashboard.alert-for-products-stock-low-table :low_stock_products="$low_stock_products" />
</div>



@endsection

@push('script')
@if (Session::has('status'))
    <x-alert :msg="Session::get('msg')" :status="Session::get('status')" />
@endif
<x-admin.scripts :getMonthlySalesOrderEarnings="$getMonthlySalesOrderEarnings" :monthlySalesOrderProductsCount="$monthlySalesOrderProductsCount" :salesTop5ProductsCount="$salesTop5ProductsCount" :currentWeekSalesEarningsAndProductsCount="$currentWeekSalesEarningsAndProductsCount" />
@endpush
