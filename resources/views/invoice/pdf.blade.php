<!-- resources/views/invoice/pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Invoice #{{ $data->id }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px auto;
            width: 90%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h5,
        h6 {
            margin: 5px 0;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-muted {
            color: #777;
        }

        .text-info {
            color: rgb(144, 144, 144);
        }

        hr {
            border: 0;
            border-top: 1px solid #eee;
            margin: 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: rgb(255, 255, 255);
            font-weight: bold;
            text-align: left;
        }

        table td i {
            margin-right: 3px;
            font-size: 12px;
        }

        .text-end {
            text-align: right;
        }

        .logo {
            display: block;
            margin-bottom: 5px;
        }

        .total-section h6 {
            margin: 5px 0;
        }

    </style>
</head>

<body>
    <div class="card">
        <!-- Header -->
        <div style="width:100%; overflow:auto; margin-bottom:20px;">
            <!-- Billed To (left) -->
            <div style="width:60%; float:left;">
                <h5 style="text-transform: uppercase; margin-bottom:5px;">Billed To</h5>
                <div style="color:#7F878F;">Order Date: {{ date('d-m-Y', strtotime($data->order_date)) }}</div>
                <div style="color:#7F878F;">Order Id: #{{ $data?->order_number }}</div>
                <div style="color:#7F878F;">Name: {{ $data?->customer?->name }}</div>
                <div style="color:#7F878F;">Phone: {{ $data?->customer?->phone }}</div>
                <div style="color:#7F878F;">Address: {{ $data?->customer?->address }}</div>
            </div>

            <!-- Invoice Info (right) -->
            <div style="width:35%; float:right; text-align:right;">
                <img src="{{ public_path('favicon_io2/android-chrome-512x512.png') }}" width="100" alt="Logo" />
            </div>
        </div>
        <div style="clear:both;"></div>
        <hr />

        <!-- Products Table -->
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd; font-size: 14px;">

            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity/Weight</th>
                    <th>Sales Status</th>
                    <th>Total</th>
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
                    function kg_or_gm_or_ft($num, $type = 'kg')
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
                @foreach ($data->sales_order_products as $order_p)
                @php
                    $qty = $order_p->stock_w_type !== 'none' ? formatNumber($order_p->qty, $order_p->stock_w_type) : (int) $order_p->qty;
                    $weight_type = $order_p->stock_w_type !== 'none' ? kg_or_gm_or_ft($order_p->qty, $order_p->stock_w_type) : ' pcs';
                @endphp
                <tr>
                    <td>#{{ $order_p->product->sku }}</td>
                    <td>{{ $order_p->product->name }}</td>

                    <td>
                        {{ $order_p->price }}BDT

                        {{-- @if ($order_p->discount_price)
                        <span style="text-decoration: line-through; color:#999; font-size: 12px;">{{ $order_p->price }}</span>
                        @endif --}}
                    </td>
                    <td>{{ $qty }}{{ $weight_type }}</td>
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
                        {{ number_format($order_p->total, 2) }}BDT
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr />

        <!-- Totals -->
        <div class="total-section text-end">
            <h6>Total: {{ number_format($data->sales_order_products->sum('total'), 2) }} BDT

            </h6>

            @if($data->payment_status === "due" || $data->payment_status === "partial due")
            <h6>Paid Amount: {{ number_format($data->paid_amount, 2) }}BDT</h6>
            <h6>Due Amount: {{ number_format($data->due_amount, 2) }}BDT</h6>
            @endif
        </div>
    </div>
</body>
</html>
