<div class="card mb-4">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-shopping-cart me-2 text-primary"></i>Order Items
            </h5>
            <button type="button" class="btn btn-primary btn-sm" onclick="addItem()">
                <i class="fas fa-plus me-1"></i>Add Item
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless" id="itemsTable">
                <thead>
                    <tr>
                        <th width="35%">Product</th>
                        <th width="15%">Quantity</th>
                        <th width="15%">Price</th>
                        <th width="10%">Tax %</th>
                        <th width="15%">Total</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody">
                    <tr class="item-row">
                        <td>
                            <select class="form-select" onchange="updatePrice(this)">
                                <option value="">Select Product</option>
                                <option value="laptop" data-price="999.00">Laptop Computer</option>
                                <option value="mouse" data-price="25.00">Wireless Mouse</option>
                                <option value="keyboard" data-price="75.00">Mechanical Keyboard</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control" value="1" min="1"
                                onchange="calculateRowTotal(this)">
                        </td>
                        <td>
                            <input type="number" class="form-control price-input" placeholder="0.00" step="0.01"
                                onchange="calculateRowTotal(this)">
                        </td>
                        <td>
                            <input type="number" class="form-control" value="10" min="0" max="100"
                                onchange="calculateRowTotal(this)">
                        </td>
                        <td>
                            <input type="text" class="form-control total-input" placeholder="0.00" readonly>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-item"
                                onclick="removeItem(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
