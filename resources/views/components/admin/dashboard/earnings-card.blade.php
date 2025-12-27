@props([
'totalSalesOrderEarningsAndMonthlyEarningsIncreaseAndDecreasePercent' => null
])

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col mt-0">
                <h5 class="card-title">Sales Earnings</h5>
            </div>

            <div class="col-auto">
                <div class="stat text-primary">
                    <i class="fa-solid fa-bangladeshi-taka-sign"></i>
                </div>
            </div>
        </div>
        <h1 class="mt-1 mb-3"><span class="bdt">৳</span>{{ $totalSalesOrderEarningsAndMonthlyEarningsIncreaseAndDecreasePercent->total_earnings }}</h1>

        <div class="mb-0">
            <span class="{{
        $totalSalesOrderEarningsAndMonthlyEarningsIncreaseAndDecreasePercent->trend === 'flat'

            ? 'text-warning'
            : ($totalSalesOrderEarningsAndMonthlyEarningsIncreaseAndDecreasePercent->trend === 'up'

                ? 'text-success'
                : 'text-danger')
    }}"><i class="mdi mdi-arrow-bottom-right"></i> {{ $totalSalesOrderEarningsAndMonthlyEarningsIncreaseAndDecreasePercent->percent }}%</span>


            <span class="text-muted">Since last month</span>
        </div>
    </div>
</div>
