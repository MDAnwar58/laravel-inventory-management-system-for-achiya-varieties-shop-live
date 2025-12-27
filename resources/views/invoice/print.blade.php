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
            color: #333;
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
            color: rgb(134, 134, 134);
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
            background-color: white;
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
        .bdt {
            font-family: 'Noto Sans Bengali', sans-serif;
            margin-right: 0.1rem;
        }
    </style>
</head>

<body onload="window.print();">

    <div class="card">
        <div style="text-align: center; margin-bottom: -0.63rem;">{{ 'বিসমিল্লাহির রহমানির রহিম' }}</div>
        <p style="text-align: center; padding-bottom: 0.25rem; margin-bottom: -0.23rem;">{{ $printing_content->short_desc }}</p>
        <h5 style="text-align: center; padding: 0.15rem 0;">Money Receipt</h5>
        <!-- Header -->
        <div style="width:100%; overflow:auto; margin-bottom:20px;">
            <!-- Billed To (left) -->
            <div style="width:60%; float:left;">
                <h5 style="text-transform: uppercase; margin-bottom:5px;">Billed To</h5>
                <div style="color:#17a2b8;">Order Date: {{ date('d-m-Y', strtotime($data->order_date)) }}</div>
                <div style="color:#17a2b8;">Order Id: #{{ $data->order_number }}</div>
                <div style="color:#17a2b8;">Name: {{ $data?->customer?->name }}</div>
                <div style="color:#17a2b8;">Phone: {{ $data?->customer?->phone }}</div>
                <div style="color:#17a2b8;">Address: {{ $data?->customer?->address }}</div>
            </div>

            <!-- Invoice Info (right) -->
            <div style="width:35%; float:right; text-align:right;">
                <div style=" display: flex; justify-content: start; align-items: center; gap:2;">
                    <img src="{{ asset('favicon_io2/android-chrome-512x512.png') }}" width="45" alt="Logo" />
                    <div style="font-size: 1.61rem;">
                        আছিয়া <div style="font-size: 12px; margin-top: -0.595rem;">ভ‍্যারাইটিস শপ</div>
                    </div>
                </div>
                <div style="text-align: start;">
                    <div style="color:#17a2b8; display:inline-block; text-align:left; white-space:normal; word-wrap:break-word;">
                        Phone: {{ $printing_content->phone_number }} / {{ $printing_content->phone_number2 }}
                    </div>
                    <div style="color:#17a2b8; display:inline-block; text-align:left; white-space:normal; word-wrap:break-word">Location: {{ $printing_content->location }}</div>
                </div>
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
                    <th>Qty</th>
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
                    function kg_or_gm_or_ft_yard_m_inchi($num, $type = 'kg')
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
                    $weight_type = $order_p->stock_w_type !== 'none' ? kg_or_gm_or_ft_yard_m_inchi($order_p->qty, $order_p->stock_w_type) : ' pcs';
                @endphp
                <tr>
                    <td>#{{ $order_p->product->sku }}</td>
                    <td>
                        <div>{{ $order_p->product->name }}</div>
                    </td>
                    <td>
                        ৳{{ number_format($order_p->price, 2) }}
                    </td>
                    <td>{{ $qty }}{{ $weight_type }}</td>
                    <td>
                        ৳{{ number_format($order_p->total, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr />
        <!-- Totals -->
        <div class="total-section text-end">
            @if($data->status !== "cancelled")
            <h6 style="font-size: 14px;">Total: ৳{{ number_format($data->sales_order_products->sum('total'), 2) }}</h6>
            <h6 style="font-size: 14px;">Paid Amount: ৳{{ number_format($data->paid_amount, 2) }}</h6>
            @else
            <h6 style="font-size: 14px;">Order Status: <span class=" text-capitalize">{{ $data->status }}</span></h6>
            @endif

            @if($data->payment_status === "due" || $data->payment_status === "partial due")
            <h6 style="font-size: 14px;">Due Amount: ৳{{ number_format($data->due_amount, 2) }}</h6>
            @endif
        </div>

        {{-- order due notes --}}
        <div style="text-align: center; padding-top: 0.55rem; font-weight: 500;">
            {{ $data->notes }}
        </div>

        {{-- footer section --}}
        {{-- <div style="padding-top: 1rem;">
            <p style="text-align: center;">সকল প্রকার হার্ডওয়ার ও স্যানিটারি সামগ্রী পাইকারি ও খুচরা বিক্রয়ের বিশেষ প্রতিষ্ঠান। কমির সপিং মার্কেট, কলারোয়া, সাতক্ষীরা। মোবাইল: ০১৯৩৬-১৪৯০০৩ / ০১৯৪০-৩০০৩০৩ </p>
            <center>
                <div style="font-size: 20px; font-weight: 700;">আছিয়া ভারাইটিস সপ</div>
                <div>https://achiya-varieties.shop/</div>
            </center>
        </div> --}}
    </div>
</body>
</html>
