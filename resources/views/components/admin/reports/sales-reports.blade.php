<div class="card">
    <div class="card-header pb-0">
        <h5 class="card-title fs-3 text-secondary">Generate Sales Reports</h5>
    </div>
    <div class="card-body pt-2">
        <div class="row align-items-end justify-content-end">
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="start-date" class="text-muted">Start Date</label>
                    <input type="date" class="form-control fs-4" id="start-date">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="end-date" class="text-muted">End Date</label>
                    <input type="date" class="form-control fs-4" id="end-date">
                </div>
            </div>
            <div class="col-md-4 col-sm-6 pt-md-0 pt-3">
                <button type="button" id="sales-report-generate-btn" class="btn btn-primary w-100 fw-medium fs-4"><i class="fa-solid fa-magnifying-glass-chart"></i> Generate Report</button>
            </div>
        </div>
    </div>
</div>

<div class="card sales-report-loading report-loading hide">
    <div class="card-body d-flex justify-content-center">
        <div class="lds-facebook"><div></div><div></div><div></div></div>
    </div>
</div>
<div class="sales-report-area">
    <div class="row">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold text-muted">
                <span class="text-secondary">Sales Reports</span>
                <div class="d-flex gap-2">
                    <span class="fw-bold text-dark" id="get-start-date"></span>
                    <span class="fw-bold text-dark">to</span>
                    <span class="fw-bold text-dark" id="get-end-date"></span>
                </div>
            </h2>
            <div>
                <button type="button" id="sales-report-export-btn" data-action="{{ route('admin.sales.reports.export') }}" class="btn btn-info text-uppercase fw-semibold">
                    export
                </button>
                <button type="button" id="sales-report-refresh-btn" class="btn btn-outline-danger text-uppercase fw-semibold">
                    <i class="fa-solid fa-retweet"></i>
                </button>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 d-flex justify-content-center align-items-center">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Orders</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis" id="total-sales-order-count">00</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 d-flex justify-content-center align-items-center">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Solded Products Of Quantity</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis d-flex align-items-center justify-content-center gap-1"><span  id="total-solded-products-count">00</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 d-flex justify-content-center align-items-center">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Solded Products Of Weights</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis d-flex align-items-center justify-content-center gap-1"><span  id="total-solded-products-weight-count">00</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 d-flex justify-content-center align-items-center">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Solded Products Of Foots</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis d-flex align-items-center justify-content-center gap-1"><span  id="total-solded-products-foot-count">00</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 d-flex justify-content-center align-items-center">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Solded Products Of Yards</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis d-flex align-items-center justify-content-center gap-1"><span  id="total-solded-products-yard-count">00</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 d-flex justify-content-center align-items-center">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Solded Products Of Meters</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis d-flex align-items-center justify-content-center gap-1"><span  id="total-solded-products-meter-count">00</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 d-flex justify-content-center align-items-center">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Earnings</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis"><span class="bdt">৳</span><span id="total-earnings">0.00</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div id="daily-sales-report-area" class="col-md-12 d-none">
            <div class="card">
                <div class="card-body">
                    <canvas id="chartjs-daily-report-line"></canvas>
                </div>
            </div>
        </div>
        <div id="paid-or-due-sales-report-area" class="col-md-12 d-none">
            <div class="card">
                <div class="card-body">
                    <canvas id="chartjs-daily-report-paid-or-due-line"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
