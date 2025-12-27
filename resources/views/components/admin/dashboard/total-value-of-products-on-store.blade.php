@props([
'totalvalueOfProductsOnStore' => 0
])

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col mt-0">
                <h5 class="card-title">Total Value Of Money</h5>
            </div>

            <div class="col-auto">
                <div class="stat text-primary">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                </div>
            </div>
        </div>
        <h1 class="mt-1 mb-3"><span class="bdt">৳</span>{{ $totalvalueOfProductsOnStore }}</h1>
        <div class="mb-0">
            <span class="text-muted">Total Value Of Products On The Store</span>
        </div>
    </div>
</div>
