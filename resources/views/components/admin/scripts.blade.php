@props([
'getMonthlySalesOrderEarnings' => null,
'monthlySalesOrderProductsCount' => null,
'salesTop5ProductsCount' => null,
'currentWeekSalesEarningsAndProductsCount' => null,
])
<script>

    function formatNumber(num, type = false) {
        // Convert input to a number safely
        num = parseFloat(num);
        if (type === true) {
            if (num < 1) {
                return parseFloat((num * 100).toFixed(2)).toString();
            }
            return parseFloat(num.toFixed(2)).toString();
        } else {
            // If value is less than 1 (like 0.99), return multiplied by 1000
            if (num < 1) {
                return (num * 1000).toString().replace(/\.0+$/, '');
            }
            // Otherwise, remove unnecessary decimals
            return parseFloat(num.toFixed(3)).toString();
        }
    }
    function kgOrGm(num) {
        // Convert input to a number safely
        num = parseFloat(num);
        // If value is less than 1 (like 0.99), return multiplied by 1000
        if (num > 0 && num < 1)return 'gm';
        if (num === 0)return '';
        // Otherwise, remove unnecessary decimals
        return 'kg'
    }
    function kgOrGmOrFtOrYardOrMOrInch(num, type = 'kg') {
        if (type === 'kg') {
            num = parseFloat(num);
            if (num < 1) {
                return 'gm';
            }
            return 'kg'
        } else if (type === 'ft') {
            num = parseFloat(num);
            if (num < 1) {
                return 'inchi';
            }
            return 'ft'
        } else if (type === 'yard') {
            num = parseFloat(num);
            if (num < 1) {
                return 'inchi';
            }
            return 'yard'
        } else if (type === 'm') {
            num = parseFloat(num);
            if (num < 1) {
                return 'inchi';
            }
            return 'm'
        }
    }
    function ftOrInchi(num) {
        num = parseFloat(num);
        if (num > 0 && num < 1)return 'inchi';
        if (num === 0)return '';
        return 'ft'
    }
    function yardOrInchi(num) {
        num = parseFloat(num);
        if (num > 0 && num < 1)return 'inchi';
        if (num === 0)return '';
        return 'yard'
    }
    function mOrInchi(num) {
        num = parseFloat(num);
        // If value is less than 1 (like 0.99), return multiplied by 1000
        if (num > 0 && num < 1)return 'inchi';
        if (num === 0)return '';
        return 'm'
    }

    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("chartjs-dashboard-line").getContext("2d");
        var gradient = ctx.createLinearGradient(0, 0, 0, 225);
        gradient.addColorStop(0, "rgba(215, 227, 244, 1)");
        gradient.addColorStop(1, "rgba(215, 227, 244, 0)");
        let monthlySalesOrderMonths = @json($getMonthlySalesOrderEarnings);
        let salesOrderMonths = []
        let monthlySales = []
        if (monthlySalesOrderMonths.length > 0) {
            monthlySalesOrderMonths.map((month) => {
                salesOrderMonths.push(month.month)
            })
        }
        if (monthlySalesOrderMonths.length > 0) {
            monthlySalesOrderMonths.map((month) => {
                monthlySales.push(month.earnings.toFixed(2))
            })
        }

        if (salesOrderMonths.length > 0 && monthlySales.length > 0) {
            // Line chart
            new Chart(document.getElementById("chartjs-dashboard-line"), {
                type: "line"
                , data: {
                    labels: salesOrderMonths
                    , datasets: [{
                        label: "Sales (৳)"
                        , fill: true
                        , backgroundColor: gradient
                        , borderColor: window.theme.primary
                        , data: monthlySales
                    }]
                }
                , options: {
                    maintainAspectRatio: false
                    , legend: {
                        display: false
                    }
                    , tooltips: {
                        intersect: false
                        , callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.yLabel.toFixed(2) + ' ৳';
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
                            reverse: true
                            , gridLines: {
                                color: "rgba(0,0,0,0.0)"
                            }
                        }]
                        , yAxes: [{
                            ticks: {
                                stepSize: 1000
                                , callback: function(value) {
                                    return value.toFixed(2);
                                }
                            }
                            , display: true
                            , borderDash: [3, 3]
                            , gridLines: {
                                color: "rgba(0,0,0,0.0)"
                            }
                        }]
                    }
                }
            });
        }
    });

</script>
<script>
   document.addEventListener("DOMContentLoaded", function() {
    // Get data from Laravel
    const dashboardPie = document.getElementById("chartjs-dashboard-pie")
    if(!dashboardPie)return;
    dashboardPie.height = 230
    var salesTop5ProductsCount = @json($salesTop5ProductsCount);

    let names = [];
    let qtys = [];
    let stockWTypes = [];


    if (salesTop5ProductsCount.length > 0) {
        salesTop5ProductsCount.forEach((product) => {
            names.push(product.name);
            // Format qty with unit
            let qtyLabel = product.stock_w_type === "none"
                ? `${product.name + ' — pcs'}`
                : `${product.name + ' — ' + kgOrGmOrFtOrYardOrMOrInch(product.total_qty, product.stock_w_type)}`;

            qtys.push(product.stock_w_type === "none"  ? parseInt(product.total_qty) : product.stock_w_type === 'kg' ? formatNumber(product.total_qty) : parseFloat(product.total_qty));
            stockWTypes.push(qtyLabel);
        });
    }

    new Chart(dashboardPie, {
        type: "pie",
        data: {
            labels: stockWTypes,  // product names only
            datasets: [{
                data: qtys,
                backgroundColor: [
                    window.theme.primary,
                    window.theme.warning,
                    window.theme.danger,
                    window.theme.info,
                    window.theme.secondary
                ],
                borderWidth: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return stockWTypes[context.dataIndex]; // "10 pcs" or "5 kg"
                        }
                    }
                }
            },
            cutout: '75%'
        }
    });
});


</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Demo weekly data
    const weeklyData = @json($currentWeekSalesEarningsAndProductsCount);
    const weekCanvas = document.getElementById("chartWeekly")

    if (!weekCanvas) return; // stop if canvas not found

    const ctxChartWeekly = weekCanvas.getContext("2d");

    let weekDays = [];
    let weekEarnings = [];
    let weekProductsCount = [];
    let weekProductWeights = [];
    let weekProductFoots = [];
    let weekProductYards = [];
    let weekProductMeters = [];

    if (weeklyData.length > 0) {
        weeklyData.forEach((day) => {
            weekDays.push(day.day);
            weekEarnings.push(day.earnings);
            weekProductsCount.push(day.products_count);
            weekProductWeights.push(day.weight_count);
            weekProductFoots.push(day.feet_count);
            weekProductYards.push(day.yard_count);
            weekProductMeters.push(day.meter_count);
        });
    }

    // Gradient for earnings line fill
    const gradient = ctxChartWeekly.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, "rgba(231, 74, 59, 0.5)");
    gradient.addColorStop(1, "rgba(231, 74, 59, 0)");

    if (weekDays.length > 0 && weekEarnings.length > 0 && weekProductsCount.length > 0 && weekProductWeights.length > 0) {
        new Chart(ctxChartWeekly, {
            type: "bar",
            data: {
                labels: weekDays,
                datasets: [
                    {
                        type: "bar",
                        label: "Products Sold",
                        data: weekProductsCount,
                        backgroundColor: "#4e73df",
                        yAxisID: "yProducts"
                    },
                    {
                        type: "bar",
                        label: "Product Weight",
                        data: weekProductWeights,
                        backgroundColor: "#1cc88a",
                        yAxisID: "yWeights"
                    },
                    {
                        type: "bar",
                        label: "Product Foot",
                        data: weekProductFoots,
                        backgroundColor: "#c122b7",
                        yAxisID: "yFeets"
                    },
                    {
                        type: "bar",
                        label: "Product Yard",
                        data: weekProductYards,
                        backgroundColor: "#2bbcb3",
                        yAxisID: "yYards"
                    },
                    {
                        type: "bar",
                        label: "Product Meter",
                        data: weekProductMeters,
                        backgroundColor: "#8fe01d",
                        yAxisID: "yMeters"
                    },
                    {
                        type: "line",
                        label: "Earnings",
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: "#e74a3b",
                        data: weekEarnings,
                        yAxisID: "yEarnings",
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: true },
                tooltips: {
                    mode: "index", // Show all datasets at hovered index
                    intersect: false,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                            const value = tooltipItem.yLabel;

                            if (tooltipItem.datasetIndex === 0) {
                                // Fixed: Products Sold
                                return datasetLabel + ": " + parseInt(value) + (parseInt(value) > 1 ? " pcs" : "");
                            } else if (tooltipItem.datasetIndex === 1) {
                                return datasetLabel + ": " + formatNumber(value) + kgOrGm(value);
                            } else if (tooltipItem.datasetIndex === 2) {
                                return datasetLabel + ": " + formatNumber(value, true) + ftOrInchi(value);
                            } else if (tooltipItem.datasetIndex === 3) {
                                return datasetLabel + ": " + formatNumber(value, true) + yardOrInchi(value);
                            } else if (tooltipItem.datasetIndex === 4) {
                                return datasetLabel + ": " + formatNumber(value, true) + mOrInchi(value);
                            } else if (tooltipItem.datasetIndex === 5) {
                                return datasetLabel + ": " + value.toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' ৳';
                            }
                            return datasetLabel + ": " + value;
                        }
                    }
                },
                hover: { intersect: true },
                scales: {
                    xAxes: [{
                        gridLines: { color: "rgba(0,0,0,0.0)" }
                    }],
                    yAxes: [
                        {
                            id: "yProducts",
                            position: "left",
                            gridLines: { color: "rgba(0,0,0,0.05)" },
                            ticks: {
                                beginAtZero: true,
                                stepSize: 5,
                                callback: function(value) { return parseInt(value); }
                            },
                            scaleLabel: { display: true, labelString: "Products Sold" }
                        },
                        {
                            id: "yWeights",
                            position: "left",
                            gridLines: { color: "rgba(0,0,0,0.05)" },
                            ticks: {
                                beginAtZero: true,
                                stepSize: 5,
                                callback: function(value) { return parseInt(value); }
                            },
                            scaleLabel: { display: true, labelString: "Products Weight" }
                        },
                        {
                            id: "yFeets",
                            position: "left",
                            gridLines: { color: "rgba(0,0,0,0.05)" },
                            ticks: {
                                beginAtZero: true,
                                stepSize: 5,
                                callback: function(value) { return parseInt(value); }
                            },
                            scaleLabel: { display: true, labelString: "Products Foot" }
                        },
                        {
                            id: "yYards",
                            position: "left",
                            gridLines: { color: "rgba(0,0,0,0.05)" },
                            ticks: {
                                beginAtZero: true,
                                stepSize: 5,
                                callback: function(value) { return parseInt(value); }
                            },
                            scaleLabel: { display: true, labelString: "Products Yard" }
                        },
                        {
                            id: "yMeters",
                            position: "left",
                            gridLines: { color: "rgba(0,0,0,0.05)" },
                            ticks: {
                                beginAtZero: true,
                                stepSize: 5,
                                callback: function(value) { return parseInt(value); }
                            },
                            scaleLabel: { display: true, labelString: "Products Meter" }
                        },
                        {
                            id: "yEarnings",
                            position: "right",
                            gridLines: { drawOnChartArea: false },
                            ticks: {
                                beginAtZero: true,
                                callback: function(value) { return value.toLocaleString('en-US', { minimumFractionDigits: 2 }) + ' ৳'; }
                            },
                            scaleLabel: { display: true, labelString: "Earnings" }
                        }
                    ]
                }
            }
        });
    }
});

</script>

<script>

document.addEventListener("DOMContentLoaded", function () {
    var monthlySalesOrderProducts = @json($monthlySalesOrderProductsCount);
    const canvas = document.getElementById("chartjs-dashboard-bar");
    if (!canvas) return; // stop if canvas not found

    const ctx = canvas.getContext("2d");
    let salesProductsMonths = [];
    let salesProductsCounts = [];
    let salesProductsWeightCounts = [];
    let salesProductsFootCounts = [];
    let salesProductsYardCounts = [];
    let salesProductsMeterCounts = [];

    if (monthlySalesOrderProducts.length > 0) {
        monthlySalesOrderProducts.forEach((month) => {
            salesProductsMonths.push(month.month);
            salesProductsCounts.push(month.count);
            salesProductsWeightCounts.push(month.weights_count);
            salesProductsFootCounts.push(month.feets_count);
            salesProductsYardCounts.push(month.yard_count);
            salesProductsMeterCounts.push(month.meter_count); // ✅ Added meter count
        });
    }

    if (salesProductsMonths.length > 0) {
        new Chart(ctx, {
            type: "bar",
            data: {
                labels: salesProductsMonths,
                datasets: [
                    {
                        label: "Sales Products Quantity",
                        data: salesProductsCounts,
                        backgroundColor: "rgba(54, 162, 235, 0.7)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1,
                        yAxisID: "yQuantity"
                    },
                    {
                        label: "Sales Products Weight",
                        data: salesProductsWeightCounts,
                        backgroundColor: "rgba(255, 99, 132, 0.7)",
                        borderColor: "rgba(255, 99, 132, 1)",
                        borderWidth: 1,
                        yAxisID: "yWeight"
                    },
                    {
                        label: "Sales Products Foot",
                        data: salesProductsFootCounts,
                        backgroundColor: "rgba(99, 255, 135, 0.7)",
                        borderColor: "rgb(99, 255, 117)",
                        borderWidth: 1,
                        yAxisID: "yFeet"
                    },
                    {
                        label: "Sales Products Yard",
                        data: salesProductsYardCounts,
                        backgroundColor: "rgba(253, 235, 158, 0.7)",
                        borderColor: "rgb(253, 235, 158)",
                        borderWidth: 1,
                        yAxisID: "yYard"
                    },
                    {
                        label: "Sales Products Meter",
                        data: salesProductsMeterCounts, // ✅ New dataset for Meter
                        backgroundColor: "rgb(11, 29, 81, 0.7)",
                        borderColor: "rgb(11, 29, 81)",
                        borderWidth: 1,
                        yAxisID: "yMeter"
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        gridLines: { display: false },
                        scaleLabel: { display: true, labelString: "Month" }
                    }],
                    yAxes: [
                        {
                            id: "yQuantity",
                            position: "right",
                            ticks: { beginAtZero: true },
                            scaleLabel: { display: true, labelString: "Quantity (pcs)" }
                        },
                        {
                            id: "yWeight",
                            position: "right",
                            ticks: { beginAtZero: true },
                            gridLines: { drawOnChartArea: false },
                            scaleLabel: { display: true, labelString: "Weight" }
                        },
                        {
                            id: "yFeet",
                            position: "right",
                            ticks: { beginAtZero: true },
                            gridLines: { drawOnChartArea: false },
                            scaleLabel: { display: true, labelString: "Foot" }
                        },
                        {
                            id: "yYard",
                            position: "right",
                            ticks: { beginAtZero: true },
                            gridLines: { drawOnChartArea: false },
                            scaleLabel: { display: true, labelString: "Yard" }
                        },
                        {
                            id: "yMeter",
                            position: "right",
                            ticks: { beginAtZero: true },
                            gridLines: { drawOnChartArea: false },
                            scaleLabel: { display: true, labelString: "Meter" } // ✅ new Y-axis
                        }
                    ]
                },
                tooltips: {
                    mode: "index",
                    intersect: false,
                    callbacks: {
                        label: function (tooltipItem, data) {
                            const datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                            const value = tooltipItem.yLabel;
                            const index = tooltipItem.index + 1;
                            if (datasetLabel.includes("Quantity")) return `${datasetLabel}: ${value} ${value > 0 ? 'pcs' : ''}`;
                            if (datasetLabel.includes("Weight")) return `${datasetLabel}: ${formatNumber(value)} ${kgOrGm(value)}`;
                            if (datasetLabel.includes("Feet")) return `${datasetLabel}: ${formatNumber(value)} ${ftOrInchi(value, true)}`;
                            if (datasetLabel.includes("Yard")) return `${datasetLabel}: ${formatNumber(value)} ${yardOrInchi(value, true)}`;
                            if (datasetLabel.includes("Meter")) return `${datasetLabel}: ${formatNumber(value)} ${mOrInchi(value, true)}`; // ✅ formatted label
                            return `${index}. ${datasetLabel}: ${value}`;
                        }
                    }
                },
                legend: { position: "bottom" }
            }
        });
    }
});




</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var date = new Date(Date.now() - 5 * 24 * 60 * 60 * 1000);
        var defaultDate = date.getUTCFullYear() + "-" + (date.getUTCMonth() + 1) + "-" + date.getUTCDate();
        document.getElementById("datetimepicker-dashboard").flatpickr({
            inline: true
            , prevArrow: "<span title=\"Previous month\">&laquo;</span>"
            , nextArrow: "<span title=\"Next month\">&raquo;</span>"
            , defaultDate: defaultDate
        });
    });

</script>
