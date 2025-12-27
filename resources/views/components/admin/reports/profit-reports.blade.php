<div class="card">
    <div class="card-header pb-0">
        <h5 class="card-title fs-3 text-secondary">Generate Profit Reports</h5>
    </div>
    <div class="card-body pt-2">
        <div class="row align-items-end justify-content-end">
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="profit-start-date" class="text-muted">Start Date</label>
                    <input type="date" class="form-control fs-4" id="profit-start-date">
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="form-group">
                    <label for="profit-end-date" class="text-muted">End Date</label>
                    <input type="date" class="form-control fs-4" id="profit-end-date">
                </div>
            </div>
            <div class="col-md-4 col-sm-6 pt-md-0 pt-3">
                <button type="button" id="profit-report-generate-btn" class="btn btn-primary w-100 fw-medium fs-4"><i class="fa-solid fa-magnifying-glass-chart"></i> Generate Report</button>
            </div>
        </div>
    </div>
</div>
<div class="card profit-report-loading report-loading hide">
    <div class="card-body d-flex justify-content-center">
        <div class="lds-facebook"><div></div><div></div><div></div></div>
    </div>
</div>
<div class="profit-report-area">
    <div class="row">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold text-muted">
                <span class="text-secondary">Profit Reports</span>
                <div class="d-flex gap-2">
                    <span class="fw-bold text-dark" id="get-profit-start-date"></span>
                    <span class="fw-bold text-dark">to</span>
                    <span class="fw-bold text-dark" id="get-profit-end-date"></span>
                </div>
            </h2>
            <div>
                <button type="button" id="profit-report-export-btn" data-action="{{ route('admin.profit.reports.export') }}" class="btn btn-info text-uppercase fw-semibold">
                    export
                </button>
                <button type="button" id="profit-report-refresh-btn" class="btn btn-outline-danger text-uppercase fw-semibold">
                    <i class="fa-solid fa-retweet"></i>
                </button>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 px-5 w-100 text-center">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Earnings</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis"><span class="bdt">৳</span><span id="total-profit-earnings">0.00</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 ">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Sales Products Of Quantity</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis" id="total-profit-sales-order-products-count">00</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 ">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Sales Products Of Weights</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis" id="total-profit-sales-order-products-weight-count">00</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 ">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Sales Products Of Foots</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis" id="total-profit-sales-order-products-foot-count">00</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 ">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Sales Products Of Yards</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis" id="total-profit-sales-order-products-yard-count">00</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 ">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Sales Products Of Meters</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis" id="total-profit-sales-order-products-meter-count">00</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="card">
                <div class="card-body total-cards-body px-5 w-100 px-5 w-100 text-center">
                    <div class="text-center">
                        <h3 class="fw-bold fs-3 text-muted text-uppercase">Gross Profit</h3>
                        <p class="fw-bold fs-3 text-secondary-emphasis"><span class="bdt">৳</span><span id="gross-profit">0.00</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div id="daily-profit-report-area" class="col-md-12 d-none">
            <div class="card">
                <div class="card-body d-flex w-100">
                    <div class="align-self-center chart chart-lg">
                        <canvas id="chartjs-daily-profit-bar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div id="product-by-profit-table-area" class="col-md-12">
            <div class="card">
                <div class="card-body w-100">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Sales Quantity/Weight/Foot/Yard/Meter</th>
                                    <th class="text-center">Sales Earnings</th>
                                    <th class="text-center">Total Profit</th>
                                </tr>
                            </thead>
                            <tbody id="product-by-profit-table-body"></tbody>
                        </table> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
