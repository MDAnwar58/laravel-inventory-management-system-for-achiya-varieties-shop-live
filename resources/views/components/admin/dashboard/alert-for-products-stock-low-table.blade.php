@props([
'low_stock_products' => []
])
<div class="col-12 col-lg-12 d-flex">
    <div class="card flex-fill">
        <div class="card-header pb-1 px-3">
            <h5 class="card-title mb-0">Low Stock Products</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover my-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Stock Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        if (!function_exists('formatNumber')) {
                            function formatNumber($num, $type = '')
                            {
                                $n = (float) $num;
                                if ($type === 'ft' || $type === 'yard' || $type === 'm') { 
                                    if ($n < 1) $repl = (string) intval(round($n * 1000 / 10));
                                    else $repl = rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
                                    return $repl;
                                } else {
                                    if ($n < 1) {
                                        return preg_replace('/\.0+$/', '', (string) ($n * 1000));
                                    }
                                    return rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
                                }
                            }
                        }

                        if (!function_exists('kg_or_gm_or_ft_yard_or_m')) {
                            function kg_or_gm_or_ft_yard_or_m($num, $type = 'kg')
                            {
                                $n = (float) $num;
                                return match($type) {
                                    'kg' => $n < 1 ? 'gm' : 'kg',
                                    'ft' => $n < 1 ? 'inchi' : 'ft',
                                    'yard' => $n < 1 ? 'inchi' : 'yard',
                                    default => $n < 1 ? 'inchi' : 'm',
                                };
                            }
                        }
                    @endphp
                    @if($low_stock_products->count() > 0)
                    @foreach($low_stock_products as $product)
                    @php
                        $stock = $product->stock_w_type !== 'none' ? formatNumber($product->stock_w, $product->stock_w_type).kg_or_gm_or_ft_yard_or_m($product->stock_w, $product->stock_w_type) : ((int)$product->stock_w)." pcs";
                        $current_stock = $product->stock_w_type !== 'none' ? (float)$product->stock_w : (int)$product->stock;
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3 fs-6" style="width: 350px;">
                                @if($product->image)
                                <img src="{{$product->image}}" alt="{{$product->name}}" style="width: 40px; height: 40px;" class="ms-2 rounded-3">
                                @else
                                <div class="border shadow rounded-3 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;"><i class="fa-solid fa-image text-secondary-emphasis fs-3"></i></div>
                                @endif
                                <div>
                                    <h6 class="fw-semibold">{{$product->name}}</h6>
                                    <p class="mb-0 fs-5 fw-medium" style="margin-top: -0.5rem;"><i class="fa-solid fa-bangladeshi-taka-sign text-muted" style="font-size: .85rem;margin-right: -0.17rem;"></i>{{$product->discount_price ? $product->discount_price : $product->price}}
                                        @if($product->discount_price)
                                        <span class="fw-lighter text-muted text-decoration-line-through" style="font-size: 12.5px;">{{$product->price}}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="width: 85px;">{{$stock}}</div>
                        </td>
                        <td>
                            <div style="width: 95px;">
                                @if($current_stock == $product->low_stock_level)
                                <span class="badge bg-warning-subtle text-warning">Stock Low</span>
                                @elseif($current_stock > 0 && $current_stock < $product->low_stock_level)
                                <span class="badge bg-warning">Stock Very Low</span>
                                @else
                                <span class="badge bg-danger">Out of Stock</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <a href="{{route('admin.product.edit', $product->id)}}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5" class="text-center py-2">Not found low stock products</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
