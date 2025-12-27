@props([
'data' => null,
'printing_content' => null,
])
<div class="col-lg-12">
    <div class="card card-body">
        <div class="row billed_row px-3">
            <div class="col-xxl-9 col-md-8 col-sm-7 col-12 order-sm-1 order-2 text-sm-start text-center pt-sm-0 pt-3">
                <h5 class="text-uppercase">billed to</h5>
                <input type="hidden" id="CId" name="customer_id">
                <div class="text-muted">Order Date: <span id="invoiceDate">{{ date('d-m-Y', strtotime($data->order_date)) }}</span></div>
                <div class="text-muted">Order Id: <span>#{{ $data->order_number }}</span></div>
                <div class="text-muted">Name: <span id="CName">{{ $data?->customer?->name }}</span></div>
                <div class="text-muted">Phone Numner: <span id="CPhone">{{ $data?->customer?->phone }}</span></div>
                <div class="text-muted">Address: <span id="CAddress">{{ $data?->customer?->address ?? "..." }}</span></div>
            </div>
            <div class="col-xxl-3 col-md-4 col-sm-5 col-12 order-sm-2 order-1 d-flex justify-content-end">
                <div class="">
                    <div class="d-flex gap-1 align-items-center justify-content-sm-start justify-content-center">
                        <img src="{{ asset('favicon_io2/android-chrome-512x512.png') }}" width="40" alt="" />
                        <div class="gradient-app-logo" style="font-size: 1.3rem;">
                            আছিয়া <div style="font-size: 10px; margin-top: -0.5rem;">ভ‍্যারাইটিস শপ</div>
                        </div>
                    </div>
                    <div class="pt-1">
                        <div class="text-muted text-wrap text-sm-start text-center" style="white-space: normal;">
                            <span class="d-sm-inline-block d-none">Phone:</span>
                            <span class="text-start">
                                {{ $printing_content?->phone_number }}, {{ $printing_content?->phone_number2 }}
                            </span>
                        </div>
                        <div class="text-muted text-wrap text-sm-start text-center" style="white-space: normal;">
                            <span class="d-sm-inline-block d-none">Location:</span>
                            <span class="text-start">
                                {{ $printing_content?->location }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="mt-3 order-3">
            <div class="col-md-12 order-4">
                <input type="hidden" name="products" id="products-input">
                <div class="table-responsive">
                    <table class="table table-hover" id="billed_table">
                        <thead>
                            <tr class="">
                                <th class="text-muted fw-normal">SKU</th>
                                <th class="text-muted fw-normal">Product</th>
                                <th class="text-muted fw-normal">Price</th>
                                <th class="text-muted fw-normal">Status</th>
                                <th class="text-muted fw-normal">Quantity/Weight</th>
                                <th class="text-muted fw-normal">Total</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceList">
                        @php
                                function formatNumber($num, $type = 'kg')
                                {
                                    $n = (float) $num;
                                    if ($type === 'kg') {
                                        if ($n < 1) {
                                            return preg_replace('/\.0+$/', '', (string) ($n * 1000));
                                        }
                                        // strip trailing .000 etc. after 3-decimal rounding
                                        return rtrim(rtrim(number_format($n, 3, '.', ''), '0'), '.');
                                    } elseif ($type === 'ft' || $type === 'yard' || $type === 'm') { 
                                        if ($n < 1)$repl = (string) intval(round($n * 1000 / 10));
                                        else $repl = rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
                                        return $repl;
                                    }
                                }
                                function kg_or_gm($num, $type = 'kg')
                                {
                                    if ($type === 'kg') {
                                        $n = (float) $num;
                                        return $n < 1 ? 'gm' : 'kg';
                                    } elseif ($type === 'ft') {
                                        $n = (float) $num;
                                        return $n < 1 ? ' inchi' : 'ft';
                                    } elseif ($type === 'yard') {
                                        $n = (float) $num;
                                        return $n < 1 ? ' inchi' : 'yard';
                                    } elseif ($type === 'm') {
                                        $n = (float) $num;
                                        return $n < 1 ? ' inchi' : 'm';
                                    }
                                }
                            @endphp
                            @foreach ($data->sales_order_products as $order_p)
                            @php
                                $qty = formatNumber($order_p->qty, $order_p->stock_w_type);
                                $weight_type = kg_or_gm($order_p->qty, $order_p->stock_w_type);
                            @endphp
                            <tr class="item-row">
                                <td>#{{ $order_p->product->sku }}</td>
                                <td>
                                    <div class="d-flex gap-2 align-items-center" style="width: 265px;">
                                        @if($order_p->product?->image)
                                        <img src="{{ $order_p->product->image }}" alt="{{ $order_p->product->name }}}" width="30" height="30" class="rounded-circle">
                                        @else
                                        <div class="border shadow rounded-circle d-flex justify-content-center align-items-center" style="width: 30px; height: 30px;">
                                            <i class="fa-solid fa-image fs-5"></i>
                                        </div>
                                        @endif
                                        {{ $order_p->product->name }}
                                    </div>
                                </td>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center" style="width: 105px;"><span class="bdt">৳</span>
                                    {{number_format($order_p->price)}}
                                    {{-- {{$order_p->discount_price ? $order_p->discount_price : $order_p->price}} --}}
                                        {{-- @if ($order_p->discount_price)
                                        <span class="ms-1 text-muted text-decoration-line-through" style="font-size: .67rem;">{{ $order_p->price }}</span>
                                        @endif --}}
                                    </div>
                                </td>
                                <td>
                                    @if(((bool)$order_p->retail_price_status) === true)
                                        <div class="badge bg-success-subtle text-success">
                                            Retail Sale
                                        </div>
                                    @else
                                        <div class="badge bg-info-subtle text-info">
                                            Wholesale Sale
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="input-group" style="width: 150px;">
                                        {{$order_p->stock_w_type !== 'none' ? $qty : (int) $order_p->qty}}{{ $order_p->stock_w_type === 'none' ? ' pcs' : $weight_type }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="bdt">৳</span>
                                        <span class="item-total">{{number_format($order_p->total, 2)}}</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12 order-5">
                <hr class="mt-3">
                <div class="d-flex justify-content-end gap-2">
                    <div>
                        @php
                        // Calculate total amounts
                        $totalAmount = 0;
                        $subTotalAmount = 0;

                        foreach ($data->sales_order_products as $order) {
                        $totalAmount += $order->total;
                        $subTotalAmount += $order->total;
                        }
                        @endphp
                        <h6 class="text-uppercase text-amount">Total:
                            <span class="bdt">৳</span>
                            <span id="total" class="text-dark">{{ number_format($totalAmount, 2) }}</span>
                        </h6>
                        @if($data->payment_status === "due" || $data->payment_status === "partial due")
                        <h6 id="paid-amount-el" class="text-uppercase text-amount">Paid Amount:
                            <span class="bdt">৳</span>
                            <span id="paid-amount">{{ number_format($data->paid_amount, 2) }}</span>
                        </h6>
                        <h6 id="due-amount-el" class="text-uppercase text-amount">Due Amount:
                            <span class="bdt">৳</span>
                            <span id="due-amount">{{ number_format($data->due_amount, 2) }}</span>
                        </h6>
                        @endif
                        <h6 class="text-uppercase text-amount">Sales Status:
                            <span class="badge {{ $data->status === 'confirmed' ? 'text-bg-success' : 'text-bg-danger' }} text-white rounded-pill">
                                {{ $data->status }}
                            </span>
                        </h6>
                        <h6 class="text-uppercase text-amount">Payment Status:
                            <span class="badge {{ 
    $data->payment_status === 'paid' 
        ? 'bg-success-subtle text-success' 
        : (
            $data->payment_status === 'partial due' 
                ? 'bg-info-subtle text-info' 
                : (
                    $data->payment_status === 'due' 
                        ? 'bg-warning-subtle text-warning' 
                        : 'bg-danger-subtle text-danger'
                                    )
                            )
                    }} rounded-pill">
                                {{ $data->payment_status }}
                            </span>
                        </h6>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3">
                    <a href="{{ route('invoice.pdf', $data->id) }}" class="btn btn-info text-uppercase fs-4 fw-semibold" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="top" data-bs-title="Generate PDF">
                        <i class="fa-solid fa-file-pdf text-white"></i>
                    </a>
                    <a href="{{route('invoice.print', $data->id)}}" target="_blank" class="btn btn-success px-2 text-uppercase d-flex justify-content-center align-items-center" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="top" data-bs-title="Print The Invoice">
                        <i class="fa-solid fa-print text-white fs-4"></i>
                    </a>
                    <a href="{{route('invoice.csv', $data->id)}}" class="btn btn-success px-2 text-uppercase d-flex justify-content-center align-items-center" data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-placement="top" data-bs-title="Export To Excel">
                        <i class="fa-solid fa-file-excel text-white fs-4"></i>
                    </a>
                    <a href="{{ route('admin.sales.order.edit', $data->id) }}" class="btn btn-primary pt-2 pb-1 rounded-3">
                        <i class="fa-solid fa-pen-to-square fs-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
