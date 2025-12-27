@props([
'salesOrderProductsFootsCountAndPercent' => null,
])
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col mt-0">
                <h5 class="card-title">Sales Products Feet</h5>
            </div>

            <div class="col-auto">
                <div class="stat text-primary">
                    <i class="fa-solid fa-scale-unbalanced-flip"></i>
                </div>
            </div>
        </div>
        <h1 class="mt-1 mb-3">{{$salesOrderProductsFootsCountAndPercent?->total_sales_order_products_foot}}</h1>


        <div class="mb-0">
            <span class="{{
        $salesOrderProductsFootsCountAndPercent->trend === 'flat'
            ? 'text-warning'
            : ($salesOrderProductsFootsCountAndPercent->trend === 'up'
                ? 'text-success'
                : 'text-danger')
    }}"><i class="mdi mdi-arrow-bottom-right"></i> {{$salesOrderProductsFootsCountAndPercent?->percent}}%</span>



            <span class="text-muted">Since last month</span>
        </div>
    </div>
</div>
