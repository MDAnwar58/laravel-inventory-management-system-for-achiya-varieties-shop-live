@props([
'salesOrderProductsUnitsCountAndPerchent' => null,
])
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col mt-0">
                <h5 class="card-title">Sales Products Quantity</h5>
            </div>

            <div class="col-auto">
                <div class="stat text-primary">
                    <i data-feather="box" class="align-middle"></i>
                </div>
            </div>
        </div>
        <h1 class="mt-1 mb-3">{{$salesOrderProductsUnitsCountAndPerchent?->total_sales_order_products_count}}</h1>


        <div class="mb-0">
            <span class="{{
        $salesOrderProductsUnitsCountAndPerchent->trend === 'flat'
            ? 'text-warning'
            : ($salesOrderProductsUnitsCountAndPerchent->trend === 'up'
                ? 'text-success'
                : 'text-danger')
    }}"><i class="mdi mdi-arrow-bottom-right"></i> {{$salesOrderProductsUnitsCountAndPerchent?->percent}}%</span>



            <span class="text-muted">Since last month</span>
        </div>
    </div>
</div>
