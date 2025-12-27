@props([
'totalCustomersCount' => 0,
])
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col mt-0">
                <h5 class="card-title">Customers</h5>
            </div>

            <div class="col-auto">
                <div class="stat text-primary">
                    <i class="align-middle" data-feather="users"></i>
                </div>
            </div>
        </div>
        <h1 class="mt-1 mb-3">{{$totalCustomersCount}}</h1>

        <div class="mb-0">
            <span class="text-muted">Total Customers</span>
        </div>
    </div>
</div>
