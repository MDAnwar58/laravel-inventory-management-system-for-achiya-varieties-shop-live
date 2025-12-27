<!-- resources/views/invoice/pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Invoice #{{ $data->id }}</title>

    <style>
        body {
             font-family: 'noto_sans_bengali', sans-serif;
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

    </style>
</head>

<body onload="window.print();">

    <div class="card">
    <div style="text-align: center; margin-bottom: -0.63rem;">{{ 'বিসমিল্লাহির রহমানির রহিম' }}</div>
    <p style="text-align: center; margin-bottom: -0.05rem;">{{ $printing_content->short_desc }}</p>
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
                <div>
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
                @foreach ($data->sales_order_products as $order_p)
                <tr>
                    <td>#{{ $order_p->product->sku }}</td>
                    <td><div style="width: 150px;">{{ $order_p->product->name }}</div></td>
                    <td>
                        ৳{{ $order_p->discount_price ?? $order_p->price }}

                        @if ($order_p->discount_price)
                        <span style="text-decoration: line-through; color:#999; font-size: 12px;">৳{{ $order_p->price }}</span>
                        @endif
                    </td>
                    <td>x{{ $order_p->qty }}</td>
                    <td>
                        ৳{{ $order_p->total }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr />
        <!-- Totals -->
        <div class="total-section text-end">
            @if($data->status !== "cancelled")
            <h6>Total: ৳{{ number_format($data->sales_order_products->sum('total'), 2) }}</h6>
            <h6>Paid Amount: ৳{{ number_format($data->paid_amount, 2) }}</h6>
            @else
            <h6>Order Status: <span class=" text-capitalize">{{ $data->status }}</span></h6>
            @endif

            @if($data->payment_status === "due" || $data->payment_status === "partial due")
            <h6>Due Amount: ৳{{ number_format($data->due_amount, 2) }}


            </h6>
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
