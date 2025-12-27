@props([
'total_products_count' => 00,
'total_products_value' => 0.00,
'total_low_stock_products_count' => 00,
'total_profits' => 0.00,
'total_low_stock_products' => [],
])
<div class="px-3">
    <div class="inventory-title d-flex justify-content-between align-items-center pb-2">
        <h5 class="fs-3 fw-semibold text-secondary">Inventory Reports</h5>
        <form action="{{ route('admin.inventory.reports.export') }}" method="GET">
            <button type="submit" class="btn btn-sm btn-info pb-1 pt-2 rounded-3" data-bs-toggle="tooltip" data-bs-title="Export To Excel"><i class="fa-solid fa-file-arrow-down fs-4"></i></button>
        </form>
    </div>
    <div class="inventory-report-area">
        <div class="row pt-1">
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="card">
                    <div class="card-body total-cards-body px-5 w-100 px-5 w-100 text-center">
                        <div class="text-center">
                            <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Products</h3>
                            <p class="fw-bold fs-3 text-secondary-emphasis">{{ $total_products_count }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="card">
                    <div class="card-body total-cards-body px-5 w-100 px-5 w-100 text-center">
                        <div class="text-center">
                            <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Values Of Products</h3>
                            <p class="fw-bold fs-3 text-secondary-emphasis"><span class="bdt">৳</span><span>{{ $total_products_value }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="card">
                    <div class="card-body total-cards-body px-5 w-100 px-5 w-100 text-center">
                        <div class="text-center">
                            <h3 class="fw-bold fs-3 text-muted text-uppercase">Low Stock Products</h3>
                            <p class="fw-bold fs-3 text-secondary-emphasis">{{ $total_low_stock_products_count }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-sm-6">
                <div class="card">
                    <div class="card-body total-cards-body px-5 w-100 px-5 w-100 text-center">
                        <div class="text-center">
                            <h3 class="fw-bold fs-3 text-muted text-uppercase">Total Profits</h3>
                            <p class="fw-bold fs-3 text-secondary-emphasis"><span class="bdt">৳</span>{{ $total_profits }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 item-type-wise-area d-none">
                <div class="card">
                    <div class="card-body w-100">
                        <h4 class="text-center fs-bold text-uppercase text-muted pb-1">Item Type Wise Products</h4>
                        <div class="chart chart-xs">
                            <canvas id="chartjs-dashboard-pie"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 brand-wise-area d-none">
                <div class="card">
                    <div class="card-body w-100">
                    <h4 class="text-center fs-bold text-uppercase text-muted pb-1">Brand Wise Products</h4>
                        <div class="chart chart-xs">
                            <canvas id="chartjs-dashboard-pie-second"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 category-wise-area d-none">
                <div class="card">
                    <div class="card-body w-100">
                    <h4 class="text-center fs-bold text-uppercase text-muted pb-1">Category Wise Products</h4>
                        <div class="chart chart-xs">
                            <canvas id="chartjs-dashboard-pie-three"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 sub-category-wise-area d-none">
                <div class="card">
                    <div class="card-body w-100">
                    <h4 class="text-center fs-bold text-uppercase text-muted pb-1">Sub Category Wise Products</h4>
                        <div class="chart chart-xs">
                            <canvas id="chartjs-dashboard-pie-four"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div id="inventory-table-area" class="col-md-12">
                <div class="card">
                    <div class="card-header pb-0 mb-0">
                        <div class="card-title pb-0 mb-0">Low Stock Products</div>
                    </div>
                    <div class="card-body pt-0 w-100">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Item Type</th>
                                        <th>Brand</th>
                                        <th>Category</th>
                                        <th>Sub Category</th>
                                        <th>Current Stock</th>
                                        <th>Price</th>
                                        <th>Retial Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php
                                        function formatNumber($num, $type = '')
                                        {
                                            $n = (float) $num;
                                            if ($type === 'ft' || $type === 'yard' || $type === 'm') { 
                                                if ($n < 1)$repl = (string) intval(round($n * 1000 / 10));
                                                else $repl = rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
                                                return $repl;
                                            } else {
                                                if ($n < 1) {
                                                    return preg_replace('/\.0+$/', '', (string) ($n * 1000));
                                                }

                                                // strip trailing .000 etc. after 3-decimal rounding
                                                return rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
                                            }
                                        }
                                        function kg_or_gm_or_ft_yard_or_m($num, $type = 'kg')
                                        {
                                            if ($type === 'kg') {
                                                $n = (float) $num;
                                                return $n < 1 ? 'gm' : 'kg';
                                            } elseif ($type === 'ft') {
                                                $n = (float) $num;
                                                return $n < 1 ? 'inchi' : 'ft';
                                            } elseif ($type === 'yard') {
                                                $n = (float) $num;
                                                return $n < 1 ? 'inchi' : 'yard';
                                            } else {
                                                $n = (float) $num;
                                                return $n < 1 ? 'inchi' : 'm';
                                            }
                                        }
                                    @endphp

                                    @if(count($total_low_stock_products) > 0)
                                    @foreach($total_low_stock_products as $key => $product)
                                    @php
                                        $stock = $product->stock_w_type !== 'none' ? formatNumber($product->stock_w, $product->stock_w_type).kg_or_gm_or_ft_yard_or_m($product->stock_w, $product->stock_w_type) : ((int)$product->stock_w)." pcs";
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-1">
                                                @if($product->image)
                                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="rounded-circle" style="width: 30px;height: 30px;">
                                                @else
                                                    <div class="border shadow rounded-3 d-flex justify-content-center align-items-center" style="width: 30px; height: 30px;"><i class="fa-solid fa-image text-secondary-emphasis fs-5"></i></div>
                                                @endif
                                                <span class="fw-bold">{{ $product->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $product->item_type?->name ?? 'N/A' }}</td>
                                        <td>{{ $product->brand?->name ?? 'N/A' }}</td>
                                        <td>{{ $product->category?->name ?? 'N/A' }}</td>
                                        <td>{{ $product->sub_category?->name ?? 'N/A' }}</td>
                                        <td>{{ $stock }}</td>
                                        <td>
                                            <div class=" text-dark"><span class="bdt">৳</span>{{ $product->price }}</div>
                                        </td>
                                        <td>
                                            <div class=" text-dark">
                                                @if($product->retail_price)
                                                <span class="bdt">৳</span>{{ $product->retail_price }}
                                                @else
                                                <span class="text-muted">N/A</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
