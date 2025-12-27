@props([
'totalOrdersCountAndMonthlySalesOrdersPercentUpAndDown' => null

])
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col mt-0">
                <h5 class="card-title">Orders</h5>
            </div>

            <div class="col-auto">
                <div class="stat text-primary">
                    <i class="align-middle" data-feather="shopping-cart"></i>
                </div>
            </div>
        </div>
        <h1 class="mt-1 mb-3">{{$totalOrdersCountAndMonthlySalesOrdersPercentUpAndDown->sales_orders_count}}</h1>
        <div class="mb-0">
            <span class="{{
        $totalOrdersCountAndMonthlySalesOrdersPercentUpAndDown->trend === 'flat'
            ? 'text-warning'
            : ($totalOrdersCountAndMonthlySalesOrdersPercentUpAndDown->trend === 'up'
                ? 'text-success'
                : 'text-danger')
    }}"><i class="mdi mdi-arrow-bottom-right"></i> {{$totalOrdersCountAndMonthlySalesOrdersPercentUpAndDown->percentage_change}}%</span>
            <span class="text-muted">Since last week</span>
        </div>
    </div>
</div>
