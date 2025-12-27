@props([
'salesOrderProductsMetersCountAndPercent' => null,
])
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col mt-0">
                <h5 class="card-title">Sales Products Meter</h5>
            </div>

            <div class="col-auto">
                <div class="stat text-primary">
                    <i class="fa-solid fa-scale-unbalanced-flip"></i>
                </div>
            </div>
        </div>
        <h1 class="mt-1 mb-3">{{$salesOrderProductsMetersCountAndPercent?->total_sales_order_products_meter}}</h1>


        <div class="mb-0">
            <span class="{{
        $salesOrderProductsMetersCountAndPercent->trend === 'flat'
            ? 'text-warning'
            : ($salesOrderProductsMetersCountAndPercent->trend === 'up'
                ? 'text-success'
                : 'text-danger')
    }}"><i class="mdi mdi-arrow-bottom-right"></i> {{$salesOrderProductsMetersCountAndPercent?->percent}}%</span>



            <span class="text-muted">Since last month</span>
        </div>
    </div>
</div>
