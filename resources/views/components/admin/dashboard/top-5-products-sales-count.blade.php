@props([
'salesTop5ProductsCount' => []
])
<div class="col-12 d-flex order-2">
    <div class="w-100">
        <div class="card flex-fill w-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Top 5 Selling Products</h5>
            </div>
            <div class="card-body d-flex" style="margin-top: -2rem;">
                <div class="align-self-center w-100">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="py-3">
                                <div class="chart chart-xs">
                                    <canvas id="chartjs-dashboard-pie"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <table class="table mb-0">
                                <tbody>
                                    @if(count($salesTop5ProductsCount) > 0)
                                        @php
                                            function formatNumber($num)
                                            {
                                                $n = (float) $num;
                                                if ($n < 1) {
                                                    return preg_replace('/\.0+$/', '', (string) ($n * 1000));
                                                }

                                                // strip trailing .000 etc. after 3-decimal rounding
                                                return rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
                                            }
                                            function kg_or_gm_or_ft($num, $type = 'kg')
                                            {
                                                if ($type === 'kg') {
                                                    $n = (float) $num;
                                                    return $n < 1 ? 'gm' : 'kg';
                                                } else {
                                                    $n = (float) $num;
                                                    return $n < 1 ? 'inchi' : 'ft';
                                                }
                                            }
                                        @endphp
                                        @foreach($salesTop5ProductsCount as $product)
                                        <tr>
                                            <td>{{$product['name']}}</td>
                                            @if($product['stock_w_type'] == 'none')
                                                <td class="text-end">{{formatNumber($product['total_qty'])}} pcs</td>
                                            @else
                                                <td class="text-end">{{formatNumber($product['total_qty'])}} {{kg_or_gm_or_ft($product['total_qty'], $product['stock_w_type'])}}</td>
                                            @endif
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
