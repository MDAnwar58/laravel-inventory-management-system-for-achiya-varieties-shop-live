@props([
    'lStockAlert' => null,
])
<div class="d-flex justify-content-between align-items-center px-4 pt-1">
    <h5 class="text-secondary fw-semibold">Low Stock Alert <span id="stock-alert-status"></span></h5>
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" data-id="{{ $lStockAlert->id }}" name="low_stock_alert" id="low-stock-alert" role="switch">
    </div>
</div>
<hr>
