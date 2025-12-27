@extends('layouts.admin-layout')
@section('title', '- Reports')

@push('style')
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400italic,400,600|Roboto+Mono:300,400">
<x-admin.reports.styles />
<style>
    .bdt {
        font-family: 'Noto Sans Bengali', sans-serif;
        margin-right: 0.1rem;
    }
</style>
@endpush

@section('content')
<x-admin.breadcrumb :breadcrumbs="$breadcrumbs" />

{{-- <x-admin.page-title title="Reports" /> --}}
<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist" style="margin-top: -0.35rem;">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="pills-sales-tab" data-bs-toggle="pill" data-bs-target="#pills-sales" type="button" role="tab" aria-controls="pills-sales" aria-selected="true">Sales Reports</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-profit-tab" data-bs-toggle="pill" data-bs-target="#pills-profit" type="button" role="tab" aria-controls="pills-profit" aria-selected="false">Profit Reports</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="pills-inventory-tab" data-bs-toggle="pill" data-bs-target="#pills-inventory" type="button" role="tab" aria-controls="pills-inventory" aria-selected="false">Inventory Reports</button>
    </li>
</ul>
<div class="tab-content" id="pills-tabContent">
    <div class="tab-pane fade show active" id="pills-sales" role="tabpanel" aria-labelledby="pills-sales-tab" tabindex="0">
        <x-admin.reports.sales-reports />
    </div>
    <div class="tab-pane fade" id="pills-profit" role="tabpanel" aria-labelledby="pills-profit-tab" tabindex="0">
        <x-admin.reports.profit-reports />
    </div>
    <div class="tab-pane fade" id="pills-inventory" role="tabpanel" aria-labelledby="pills-inventory-tab" tabindex="0">
        <x-admin.reports.inventory-reports :total_products_count="$total_products_count" :total_products_value="$total_products_value" :total_low_stock_products_count="$total_low_stock_products_count" :total_low_stock_products="$total_low_stock_products" :total_profits="$total_profits" />
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<x-admin.reports.scripts :proucts_count_by_items="$proucts_count_by_items" :proucts_count_by_brands="$proucts_count_by_brands" :proucts_count_by_categories="$proucts_count_by_categories"  :proucts_count_by_sub_categories="$proucts_count_by_sub_categories" />
<x-admin.tool-tip />
<script>
    let startDate = "";
    let endDate = "";
    let dailySalesReport = null;
    let paidOrDueSalesReport = null;
    let profitStartDate = "";
    let profitEndDate = "";
    let dailyProfitReport = null;

    const setStartDate = (date) => startDate = date;
    const setEndDate = (date) => endDate = date;
    const setProfitStartDate = (date) => profitStartDate = date;
    const setProfitEndDate = (date) => profitEndDate = date;

    function dateFormat(date, symbol = "/") {
        const dateObj = new Date(date);
        const day = String(dateObj.getDate()).padStart(2, '0');
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const year = dateObj.getFullYear();
        return `${day}${symbol}${month}${symbol}${year}`;
    }

    function resetCanvas(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext("2d");

        // Clear the canvas fully before drawing again
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        return ctx;
    }
    
    function formatNumber(num) {
        // Convert input to a number safely
        num = parseFloat(num);

        // If value is less than 1 (like 0.99), return multiplied by 1000
        if (num < 1) {
            return (num * 1000).toString().replace(/\.0+$/, '');
        }

        // Otherwise, remove unnecessary decimals
        return parseFloat(num.toFixed(3)).toString();
    }
    function kgOrGm(num) {
        num = parseFloat(num);
        return num < 1 ? num > 0 && num < 1 ? 'gm' : '' : 'kg'
    }
    function ftOrInchi(num) {
        num = parseFloat(num);
        return num < 1 ? num > 0 && num < 1 ? 'inchi' : '' : 'ft'
    }
    function yardOrInchi(num) {
        num = parseFloat(num);
        return num < 1 ? num > 0 && num < 1 ? 'inchi' : '' : 'yard'
    }
    function mOrInchi(num) {
        num = parseFloat(num);
        return num < 1 ? num > 0 && num < 1 ? 'inchi' : '' : 'm'
    }



    document.addEventListener('DOMContentLoaded', function() {
        /* sales report for that start */
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        const salesReportArea = document.querySelector('.sales-report-area');
        const salesReportGenerateBtn = document.getElementById('sales-report-generate-btn');
        const salesReportRefreshBtn = document.getElementById('sales-report-refresh-btn');
        const salesReportExportBtn = document.getElementById('sales-report-export-btn');
        const pillsSalesTab = document.getElementById('pills-sales-tab')

        startDateInput.value = ""
        endDateInput.value = ""
        /* sales report for that end */

        /* profit report for that start */
        const profitReportGenerateBtn = document.getElementById("profit-report-generate-btn")
        const profitStartDateInput = document.getElementById("profit-start-date")
        const profitEndDateInput = document.getElementById("profit-end-date")
        const profitReportArea = document.querySelector('.profit-report-area');
        const pillsProfitTab = document.getElementById('pills-profit-tab')
        const profitReportRefreshBtn = document.getElementById('profit-report-refresh-btn')
        const profitReportExportBtn = document.getElementById('profit-report-export-btn')
        const salesReportLoading = document.querySelector('.sales-report-loading')
        const profitReportLoading = document.querySelector('.profit-report-loading')

        profitStartDateInput.value = ""
        profitEndDateInput.value = ""
        /* profit report for that end */
        const pillsInventoryTab = document.getElementById('pills-inventory-tab')

        /* sales report for that start */
        startDateInput.addEventListener('change', function(e) {
            setStartDate(e.currentTarget.value);
        });
        endDateInput.addEventListener('change', function(e) {
            setEndDate(e.currentTarget.value);
        });
        salesReportGenerateBtn.addEventListener('click', async function() {
            salesReportArea.classList.remove('show');
            salesReportLoading.classList.remove('hide')
            salesReportLoading.classList.add('show')
            try {
                const res = await axios.get('/admin/sales-reports', {
                    params: {
                        start_date: startDate
                        , end_date: endDate
                    }
                });
                
                if (res.status === 200) {
                    setSalesReport(res.data);
                    salesReportLoading.classList.add('hide')
                    salesReportLoading.classList.remove('show')
                    salesReportArea.classList.add('show');
                }
            } catch (error) {
                console.error('error:', error);
                salesReportLoading.classList.add('hide')
                salesReportLoading.classList.remove('show')
                salesReportArea.classList.add('show');
            }
        });


        if (salesReportExportBtn) {
            salesReportExportBtn.addEventListener('click', function(e) {
                let url = e.currentTarget.dataset.action + '?start_date=' + startDate + '&end_date=' + endDate;
                window.location.href = url;
            });
        }

        salesReportRefreshBtn.addEventListener('click', function() {
            clearSalesReport()
        });
        pillsSalesTab.addEventListener('click', function() {
            clearProfitReport()
        });
        /* sales report for that end */

        /* profit report for that start */
        profitStartDateInput.addEventListener('change', function(e) {
            setProfitStartDate(e.currentTarget.value)
        });
        profitEndDateInput.addEventListener('change', function(e) {
            setProfitEndDate(e.currentTarget.value)
        });
        profitReportGenerateBtn.addEventListener('click', async function(e) {
            profitReportArea.classList.remove('show');
            profitReportLoading.classList.remove('hide')
            profitReportLoading.classList.add('show')
            try {
                const res = await axios.get(`/admin/profit-reports?start_date=${profitStartDate}&end_date=${profitEndDate}`)
                console.log(res);
                if (res.status === 200) {
                    setProfitReport(res.data)
                    profitReportLoading.classList.add('hide')
                    profitReportLoading.classList.remove('show')
                    profitReportArea.classList.add('show');
                }
            } catch (error) {
                console.log(error)
                profitReportLoading.classList.add('hide')
                profitReportLoading.classList.remove('show')
                profitReportArea.classList.add('show');
            }
        });

        profitReportRefreshBtn.addEventListener('click', function() {
            clearProfitReport()
        });
        pillsProfitTab.addEventListener('click', function() {
            clearSalesReport()
        });
        if (profitReportExportBtn) {
            profitReportExportBtn.addEventListener('click', function(e) {
                let url = `${e.currentTarget.dataset.action}?start_date=${profitStartDate}&end_date=${profitEndDate}`;
                window.location.href = url;
            });
        }
        /* profit report for that end */

        pillsInventoryTab.addEventListener('click', function() {
            clearSalesReport()
            clearProfitReport()
        });
    });

    function clearSalesReport() {
        const salesReportArea = document.querySelector('.sales-report-area');
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        salesReportArea.classList.remove('show');
        setSalesReport([]);
        setStartDate("")
        setEndDate("")
        startDateInput.value = ""
        endDateInput.value = ""
    }

    function clearProfitReport() {
        const profitReportArea = document.querySelector('.profit-report-area');
        const profitStartDateInput = document.getElementById("profit-start-date")
        const profitEndDateInput = document.getElementById("profit-end-date")
        profitReportArea.classList.remove('show');
        setProfitReport([]);
        setProfitEndDate("")
        setProfitStartDate("")
        profitStartDateInput.value = ""
        profitEndDateInput.value = ""
    }

    /* sales report for that start */
    function setSalesReport(data) {
        const getStartDate = document.getElementById('get-start-date')
        const getEndDate = document.getElementById('get-end-date')
        const totalSalesOrderCount = document.getElementById('total-sales-order-count')
        const totalEarnings = document.getElementById('total-earnings')
        const totalSoldedProductsCount = document.getElementById('total-solded-products-count')
        const totalSoldedProductsWeightCount = document.getElementById('total-solded-products-weight-count')
        const totalSoldedProductsFootCount = document.getElementById('total-solded-products-foot-count')
        const totalSoldedProductsYardCount = document.getElementById('total-solded-products-yard-count')
        const totalSoldedProductsMeterCount = document.getElementById('total-solded-products-meter-count')


        if (data && data?.date?.start) getStartDate.innerText = dateFormat(data?.date?.start);
        else getStartDate.innerText = '';
        if (data && data?.date?.end) getEndDate.innerText = dateFormat(data?.date?.end);
        else getEndDate.innerText = '';
        if (data && data.total_sales_orders_count) totalSalesOrderCount.innerText = data.total_sales_orders_count;
        else totalSalesOrderCount.innerText = '00';
        if (data && data.total_sales_products_weights) totalSoldedProductsWeightCount.innerText = data.total_sales_products_weights;
        else totalSoldedProductsWeightCount.innerText = '00';
        if (data && data.total_sales_products_foots) totalSoldedProductsFootCount.innerText = data.total_sales_products_foots;
        else totalSoldedProductsFootCount.innerText = '00';
        if (data && data.total_sales_products_yards) totalSoldedProductsYardCount.innerText = data.total_sales_products_yards;
        else totalSoldedProductsYardCount.innerText = '00';
        if (data && data.total_sales_products_meters) totalSoldedProductsMeterCount.innerText = data.total_sales_products_meters;
        else totalSoldedProductsMeterCount.innerText = '00';
        if (data && data.total_sales_earnings) totalEarnings.innerText = data.total_sales_earnings;
        else totalEarnings.innerText = '0.00';
        if (data && data.total_sales_products_units) totalSoldedProductsCount.innerText = data.total_sales_products_units;
        else totalSoldedProductsCount.innerText = '00';
        if (data && data.daily_sales) getDailySalesEarningsAndProductsCountReportChart(data.daily_sales);
        else getDailySalesEarningsAndProductsCountReportChart([]);
        if (data && data.daily_paid_and_due_sales) getDailySalesPaidAndDueReportChart(data.daily_paid_and_due_sales);
        else getDailySalesPaidAndDueReportChart([]);
    }

    function getDailySalesEarningsAndProductsCountReportChart(dailySales) {
        const dailySalesReportArea = document.getElementById("daily-sales-report-area");
        const ctx = resetCanvas("chartjs-daily-report-line");

        if (dailySales.length === 0) {
            if (dailySalesReport) {
                dailySalesReport.destroy();
                dailySalesReport = null;
            }
            dailySalesReportArea.classList.add("d-none");
            console.log("No daily sales data found.");
            return;
        }

        dailySalesReportArea.classList.remove("d-none");

        if (dailySalesReport) {
            dailySalesReport.destroy();
            dailySalesReport = null;
        }
 
        // 🟩 Extract data
        const dates = dailySales.map(item => dateFormat(item.date, "-"));
        const sales_earnings = dailySales.map(item => item.total_sales);
        const products_counts = dailySales.map(item => item.total_products_count);
        const total_products_weights = dailySales.map(item => item.total_products_weights);
        const total_products_foots = dailySales.map(item => item.total_products_foots);
        const total_products_yards = dailySales.map(item => item.total_products_yards);
        const total_products_meters = dailySales.map(item => item.total_products_meters);

        // 🟦 Dynamic chart height based on window size
        function setResponsiveChartHeight() {
            const el = ctx.canvas;
            const w = window.innerWidth;
            let height = 221;

            if (w > 1325) height = 421;
            else if (w > 1175) height = 351;
            else if (w > 1075) height = 301;
            else if (w > 992) height = 251;
            else if (w > 885) height = 401;
            else if (w > 850) height = 351;
            else if (w > 805) height = 321;
            else if (w > 768) height = 301;
            else if (w > 675) height = 271;
            else if (w > 575) height = 251;

            el.setAttribute("height", height);
        }

        // 🟧 Throttled resize event
        let resizeTimeout;
        window.addEventListener("resize", () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(setResponsiveChartHeight, 150);
        });

        setResponsiveChartHeight();

        // 🟨 Gradient for earnings
        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, "rgba(75,192,192,0.4)");
        gradient.addColorStop(1, "rgba(75,192,192,0)");

        // 🟩 Chart.js configuration
        dailySalesReport = new Chart(ctx, {
            type: "line",
            data: {
                labels: dates,
                datasets: [
                    {
                        label: "Sales Earnings (৳)",
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: "#2196F3",
                        data: sales_earnings,
                        yAxisID: "y-axis-1",
                    },
                    {
                        label: "Solded Quantity",
                        fill: false,
                        borderColor: "#FF9800",
                        backgroundColor: "rgba(255, 152, 0, 0.2)",
                        
                        data: products_counts,
                        yAxisID: "y-axis-2",
                    },
                    {
                        label: "Solded Weights",
                        fill: false,
                        borderColor: "#4CAF50",
                        backgroundColor: "rgba(76, 175, 80, 0.2)",
                        //borderDash: [5, 5], // dashed line
                        data: total_products_weights,
                        yAxisID: "y-axis-3",
                    },
                     {
                        label: "Solded Foots",
                        fill: false,
                        borderColor: "#E91E63", // 🔸 different color from weight
                        backgroundColor: "rgba(233, 30, 99, 0.2)",
                        data: total_products_foots,
                        yAxisID: "y-axis-4", // separate axis for better scaling
                    },
                    {
                        label: "Solded Yards",
                        fill: false,
                        borderColor: "#9C27B0", // 🟣 Purple for yard
                        backgroundColor: "rgba(156, 39, 176, 0.2)",
                        data: total_products_yards,
                        yAxisID: "y-axis-5",
                    },
                    {
                        label: "Solded Meters",
                        fill: false,
                        borderColor: "#009688", // 🟩 Teal for meter
                        backgroundColor: "rgba(0, 150, 136, 0.2)",
                        data: total_products_meters,
                        yAxisID: "y-axis-6",
                    },
                ],
            },
            options: {
                responsive: true, // still allows full responsiveness
                maintainAspectRatio: false,
                legend: { display: true },
                tooltips: {
                    intersect: false,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            const label = data.datasets[tooltipItem.datasetIndex].label || "";
                            const value = tooltipItem.yLabel;
                            if (tooltipItem.datasetIndex === 0) return `${label}: ${value.toFixed(2)} ৳`;
                            else if (tooltipItem.datasetIndex === 2) return `${label}: ${formatNumber(value)}${kgOrGm(value)}`;
                            else if (tooltipItem.datasetIndex === 3) return `${label}: ${value}${ftOrInchi(value)}`;
                            else if (tooltipItem.datasetIndex === 4) return `${label}: ${value}${yardOrInchi(value)}`;
                            else if (tooltipItem.datasetIndex === 5) return `${label}: ${value}${mOrInchi(value)}`;
                            return `${label}: ${value+' pcs'}`;
                        },
                    },
                },
                hover: { intersect: true },
                plugins: { filler: { propagate: false } },
                scales: {
                    xAxes: [{ gridLines: { color: "rgba(0,0,0,0.0)" } }],
                    yAxes: [
                        {
                            id: "y-axis-1",
                            position: "left",
                            ticks: { callback: value => value.toFixed(2) + " ৳" },
                            gridLines: { color: "rgba(0,0,0,0.05)" },
                        },
                        {
                            id: "y-axis-2",
                            position: "right",
                            ticks: { beginAtZero: true },
                            gridLines: { drawOnChartArea: false },
                        },
                        {
                            id: "y-axis-3",
                            position: "right",
                            ticks: { beginAtZero: true, callback: value => value.toFixed(2) + " kg" },
                            gridLines: { drawOnChartArea: false },
                        },
                        {
                            id: "y-axis-4",
                            position: "right",
                            ticks: { beginAtZero: true, callback: value => value.toFixed(2) + " ft" },
                            gridLines: { drawOnChartArea: false },
                        },
                        {
                            id: "y-axis-5",
                            position: "right",
                            ticks: { beginAtZero: true, callback: value => value.toFixed(2) + " yd" },
                            gridLines: { drawOnChartArea: false },
                        },
                        {
                            id: "y-axis-6",
                            position: "right",
                            ticks: { beginAtZero: true, callback: value => value.toFixed(2) + " m" },
                            gridLines: { drawOnChartArea: false },
                        },
                    ],
                },
            },
        });
    }


    function getDailySalesPaidAndDueReportChart(dataDailyPaidAndDueSales) {
        const paidOrDueSalesReportArea = document.getElementById("paid-or-due-sales-report-area")
        const ctx_paid_or_due = resetCanvas("chartjs-daily-report-paid-or-due-line");
        if (dataDailyPaidAndDueSales.length > 0) {
            if (paidOrDueSalesReport) {
                paidOrDueSalesReport.destroy();
                paidOrDueSalesReport = null;
            }

            paidOrDueSalesReportArea.classList.remove('d-none');
            pDDates = dataDailyPaidAndDueSales.map((item) => dateFormat(item.date, "-"))
            dailyPaidAmounts = dataDailyPaidAndDueSales.map((item) => item.total_paid)
            dailyDueAmounts = dataDailyPaidAndDueSales.map((item) => item.total_due)


            function getWindowSizeForPaidOrDue(el) {
                const windowWidth = window.innerWidth;
                if (windowWidth > 1325) el.setAttribute("height", 421);
                else if (windowWidth > 1175) el.setAttribute("height", 351);
                else if (windowWidth > 1075) el.setAttribute("height", 301);
                else if (windowWidth > 992) el.setAttribute("height", 251);
                else if (windowWidth > 885) el.setAttribute("height", 401);
                else if (windowWidth > 850) el.setAttribute("height", 351);
                else if (windowWidth > 805) el.setAttribute("height", 321);
                else if (windowWidth > 768) el.setAttribute("height", 301);
                else if (windowWidth > 675) el.setAttribute("height", 271);
                else if (windowWidth > 575) el.setAttribute("height", 251);
                else if (windowWidth > 475) el.setAttribute("height", 221);
            }

            // ✅ Pass canvas manually
            window.addEventListener("resize", () => getWindowSizeForPaidOrDue(ctx_paid_or_due.canvas));

            // Run once at load
            getWindowSizeForPaidOrDue(ctx_paid_or_due.canvas);


            const gradient_paid_or_due = ctx_paid_or_due.createLinearGradient(0, 0, 0, 200);
            gradient_paid_or_due.addColorStop(0, "rgba(75,192,192,0.4)");
            gradient_paid_or_due.addColorStop(1, "rgba(75,192,192,0)");

            // 🔹 Chart setup
            paidOrDueSalesReport = new Chart(ctx_paid_or_due, {
                type: "line"
                , data: {
                    labels: pDDates
                    , datasets: [{
                            label: "Total Paid (৳)"
                            , fill: true
                            , backgroundColor: gradient_paid_or_due
                            , borderColor: "#2196F3"
                            , data: dailyPaidAmounts
                            , yAxisID: "y-axis-1"
                        }
                        , {
                            label: "Total Due (৳)"
                            , fill: false
                            , borderColor: "#FF9800"
                            , backgroundColor: "rgba(255, 152, 0, 0.2)"
                            , data: dailyDueAmounts
                            , yAxisID: "y-axis-2"
                        }
                    ]
                }
                , options: {
                    maintainAspectRatio: false
                    , legend: {
                        display: true
                    }
                    , tooltips: {
                        intersect: false
                        , callbacks: {
                            label: function(tooltipItem, data) {
                                const datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                                if (tooltipItem.datasetIndex === 0) {
                                    return datasetLabel + ": " + tooltipItem.yLabel.toFixed(2) + ' ৳';
                                } else {
                                    return datasetLabel + ": " + tooltipItem.yLabel;
                                }
                            }
                        }
                    }
                    , hover: {
                        intersect: true
                    }
                    , plugins: {
                        filler: {
                            propagate: false
                        }
                    }
                    , scales: {
                        xAxes: [{
                            gridLines: {
                                color: "rgba(0,0,0,0.0)"
                            }
                        }]
                        , yAxes: [{
                                id: "y-axis-1"
                                , position: "left"
                                , ticks: {
                                    callback: function(value) {
                                        return value.toFixed(2) + " ৳";
                                    }
                                }
                                , gridLines: {
                                    color: "rgba(0,0,0,0.05)"
                                }
                            }
                            , {
                                id: "y-axis-2"
                                , position: "right"
                                , ticks: {
                                    beginAtZero: true
                                }
                                , gridLines: {
                                    drawOnChartArea: false
                                }
                            }
                        ]
                    }
                }
            });
        } else {
            if (paidOrDueSalesReport) {
                paidOrDueSalesReport.destroy();
                paidOrDueSalesReport = null;
            }
            paidOrDueSalesReportArea.classList.add('d-none');
            console.log("No daily sales data found.");
        }
    }
    /* sales report for that end */

    /* profit report for that start */
    function setProfitReport(data) {
        const getProfitStartDate = document.getElementById('get-profit-start-date')
        const getProfitEndDate = document.getElementById('get-profit-end-date')
        const totalProfitEarnings = document.getElementById('total-profit-earnings')
        const totalProfitSalesOrderProductsCount = document.getElementById('total-profit-sales-order-products-count')
        const totalProfitSalesOrderProductsWeightCount = document.getElementById('total-profit-sales-order-products-weight-count')
        const totalProfitSalesOrderProductsFootCount = document.getElementById('total-profit-sales-order-products-foot-count')
        const totalProfitSalesOrderProductsYardCount = document.getElementById('total-profit-sales-order-products-yard-count')
        const totalProfitSalesOrderProductsMeterCount = document.getElementById('total-profit-sales-order-products-meter-count')
        const grossProfit = document.getElementById('gross-profit')

        //console.log(data);

        if (data && data.start_date) getProfitStartDate.innerText = dateFormat(data.start_date, "/");
        else getProfitStartDate.innerText = ''
        if (data && data.end_date) getProfitEndDate.innerText = dateFormat(data.end_date, "/");
        else getProfitEndDate.innerText = ''
        if (data && data.total_earnings) totalProfitEarnings.innerText = data.total_earnings;
        else totalProfitEarnings.innerText = ''
        if (data && data.total_sales_products_count) totalProfitSalesOrderProductsCount.innerText = data.total_sales_products_count;
        else totalProfitSalesOrderProductsCount.innerText = '0.00';
        if (data && data.total_sales_products_weight_count) totalProfitSalesOrderProductsWeightCount.innerText = data.total_sales_products_weight_count;
        else totalProfitSalesOrderProductsWeightCount.innerText = '0.00';
        if (data && data.total_sales_products_foot_count) totalProfitSalesOrderProductsFootCount.innerText = data.total_sales_products_foot_count;
        else totalProfitSalesOrderProductsFootCount.innerText = '0.00';
        if (data && data.total_sales_products_yard_count) totalProfitSalesOrderProductsYardCount.innerText = data.total_sales_products_yard_count;
        else totalProfitSalesOrderProductsYardCount.innerText = '0.00';
        if (data && data.total_sales_products_meter_count) totalProfitSalesOrderProductsMeterCount.innerText = data.total_sales_products_meter_count;
        else totalProfitSalesOrderProductsMeterCount.innerText = '0.00';
        if (data && data.gross_profit) grossProfit.innerText = data.gross_profit.toFixed(2);
        else grossProfit.innerText = '0.00';
        if (data && data.daily_profits && data.daily_profits.length > 0) totalStartAndEndDateWithProfitReportChart(data.daily_profits);
        else totalStartAndEndDateWithProfitReportChart([]);
        if (data && data.profit_by_products && data.profit_by_products.length > 0) salesByProductReport(data.profit_by_products);
        else salesByProductReport([]);
    }

    function totalStartAndEndDateWithProfitReportChart(dailyProfits) {
        const dailyProfitReportArea = document.getElementById('daily-profit-report-area');
        const profitCtx = resetCanvas("chartjs-daily-profit-bar");
        //console.log(dailyProfits);
        if (dailyProfits.length > 0) {
            dailyProfitReportArea.classList.remove('d-none');

            if (dailyProfitReport) {
                dailyProfitReport.destroy();
                dailyProfitReport = null;
            }

            // ✅ Demo data
            let dates = [];
            let profits = [];

            dailyProfits.map((order) => {
                dates.push(order.date);
            });
            dailyProfits.map((order) => {
                profits.push(order.profit);
            });

            if (dates.length > 0 && profits.length > 0) {
                // Bar chart
                dailyProfitReport = new Chart(profitCtx, {
                    type: "bar"
                    , data: {
                        labels: dates
                        , datasets: [{
                            label: "Profit  (৳)"
                            , backgroundColor: "#2196F3", // 🔵 replaced window.theme.primary
                            borderColor: "#2196F3"
                            , hoverBackgroundColor: "#1976D2"
                            , hoverBorderColor: "#1976D2"
                            , data: profits
                            , barPercentage: .75
                            , categoryPercentage: .5
                        }]
                    }
                    , options: {
                        maintainAspectRatio: false
                        , tooltips: {
                            intersect: false
                            , callbacks: {
                                label: function(tooltipItem, data) {
                                    const datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                                    if (tooltipItem.datasetIndex === 0) {
                                        return datasetLabel + ": " + tooltipItem.yLabel.toFixed(2);
                                    } else {
                                        return datasetLabel + ": " + tooltipItem.yLabel;
                                    }
                                }
                            }
                        }
                        , legend: {
                            display: false
                        }
                        , scales: {
                            yAxes: [{
                                gridLines: {
                                    display: false
                                }
                                , stacked: false
                                , ticks: {
                                    stepSize: 20,
                                    // ✅ Show decimals on Y axis
                                    callback: function(value) {
                                        return value.toFixed(2); // always 2 decimal places
                                    }
                                }
                            }]
                            , xAxes: [{
                                stacked: false
                                , gridLines: {
                                    color: "transparent"
                                }
                            , }]
                        }
                    }
                });
            }
        } else {
            if (dailyProfitReport) {
                dailyProfitReport.destroy();
                dailyProfitReport = null;
            }
            dailyProfitReportArea.classList.add('d-none');
        }
    }

    function salesByProductReport(products) {
        // console.log(products);
        const productByProfitTableArea = document.getElementById('product-by-profit-table-area');
        const productByProfitTableBody = document.getElementById('product-by-profit-table-body');
        productByProfitTableBody.innerHTML = '';
        if (products.length > 0) {
            productByProfitTableArea.classList.remove('d-none');
            products.forEach((product) => {
                productByProfitTableBody.innerHTML += `<tr>
                    <td>
                        <div class="d-flex flex-row align-items-center gap-2">
                            ${product.image 
                            ? `<img src="${product.image}" alt="" class="rounded" style="width: 50px;">` 
                            : `<div class="border shadow rounded-3 d-flex justify-content-center align-items-center" style="width: 50px; height: 50px;"><i class="fa-solid fa-image text-secondary-emphasis fs-3"></i></div>`}
                            <span class="ml-2 d-inline-block">${product.name}</span>
                        </div>
                    </td>
                    <td class="text-center">${product.total_units}</td>
                    <td class="text-center"><span class="bdt">৳</span>${product.total_sales.toFixed(2)}</td>
                    <td class="text-center"><span class="bdt">৳</span>${product.total_profit.toFixed(2)}</td>
                </tr>`
            })
        }
    }

    /* profit report for that end */

</script>
@endpush
