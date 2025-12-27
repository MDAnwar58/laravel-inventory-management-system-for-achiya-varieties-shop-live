@props([
'salesOrderProductsCountAndPerchent' => null,
])
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col mt-0">
                <h5 class="card-title">Sales Products</h5>
            </div>

            <div class="col-auto">
                <div class="stat text-primary">
                    <i class="align-middle" data-feather="truck"></i>
                </div>
            </div>
        </div>
        <h1 class="mt-1 mb-3">{{$salesOrderProductsCountAndPerchent?->total_sales_order_products_count}}</h1>


        <div class="mb-0">
            <span class="{{
        $salesOrderProductsCountAndPerchent->trend === 'flat'
            ? 'text-warning'
            : ($salesOrderProductsCountAndPerchent->trend === 'up'
                ? 'text-success'
                : 'text-danger')
    }}"><i class="mdi mdi-arrow-bottom-right"></i> {{$salesOrderProductsCountAndPerchent?->percent}}%</span>



            <span class="text-muted">Since last month</span>
        </div>
    </div>
</div>
