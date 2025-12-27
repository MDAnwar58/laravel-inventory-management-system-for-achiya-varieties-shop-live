@props([
    'proucts_count_by_items' => [],
    'proucts_count_by_brands' => [],
    'proucts_count_by_categories' => [],
    'proucts_count_by_sub_categories' => [],
])

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Demo data: categories with product counts
    const itemTypeWiseArea = document.querySelector('.item-type-wise-area');
    var itemsByProductCounts = @json($proucts_count_by_items);
    if (itemsByProductCounts.length > 0)itemTypeWiseArea.classList.remove('d-none');
    // Prepare arrays for chart
    let itemTypes = itemsByProductCounts.map(item => item.name);
    let counts = itemsByProductCounts.map(item => item.products_count);

    // Pie chart
    new Chart(document.getElementById("chartjs-dashboard-pie"), {
        type: "pie",
        data: {
            labels: itemTypes,
            datasets: [{
                data: counts,
                backgroundColor: [
                    "#4e73df", // blue
                    "#f6c23e", // yellow
                    "#e74a3b", // red
                    "#36b9cc", // teal
                    "#858796"  // gray
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
                }
            }
        }
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Demo data: categories with product counts
    const brandWiseArea = document.querySelector('.brand-wise-area');
    var brandProductCounts = @json($proucts_count_by_brands);
    if (brandProductCounts.length > 0)brandWiseArea.classList.remove('d-none');
    // Prepare arrays for chart
    let brands = brandProductCounts.map(item => item.name);
    let counts = brandProductCounts.map(item => item.products_count);

    // Pie chart
    new Chart(document.getElementById("chartjs-dashboard-pie-second"), {
        type: "pie",
        data: {
            labels: brands,
            datasets: [{
                data: counts,
                backgroundColor: [
                    "#F25912", // teal
                    "#5C3E94", // blue
                    "#412B6B", // yellow
                    "#211832", // red
                    "#858796"  // gray
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
                }
            }
        }
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Demo data: categories with product counts
    const categoryWiseArea = document.querySelector('.category-wise-area');
    var categoryProductCounts = @json($proucts_count_by_categories);
    if (categoryProductCounts.length > 0)categoryWiseArea.classList.remove('d-none');
    // Prepare arrays for chart
    let categories = categoryProductCounts.map(item => item.name);
    let counts = categoryProductCounts.map(item => item.products_count);

    // Pie chart
    new Chart(document.getElementById("chartjs-dashboard-pie-three"), {
        type: "pie",
        data: {
            labels: categories,
            datasets: [{
                data: counts,
                backgroundColor: [
                    "#F7374F", // blue
                    "#88304E", // yellow
                    "#e74a3b", // red
                    "#4FB7B3", // teal
                    "#739EC9"  // gray
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
                }
            }
        }
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Demo data: categories with product counts
    const subCategoryWiseArea = document.querySelector('.sub-category-wise-area');
    var subCategoryProductCounts = @json($proucts_count_by_sub_categories);
    if (subCategoryProductCounts.length > 0)subCategoryWiseArea.classList.remove('d-none');
    // Prepare arrays for chart
    let subCategories = subCategoryProductCounts.map(item => item.name);
    let counts = subCategoryProductCounts.map(item => item.products_count);

    // Pie chart
    new Chart(document.getElementById("chartjs-dashboard-pie-four"), {
        type: "pie",
        data: {
            labels: subCategories,
            datasets: [{
                data: counts,
                backgroundColor: [
                    "#3B38A0", // blue
                    "#B2B0E8", // yellow
                    "#67C090", // red
                    "#26667F", // teal
                    "#858796"  // gray
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
                }
            }
        }
    });
});
</script>