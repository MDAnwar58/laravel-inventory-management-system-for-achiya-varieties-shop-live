<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Low Stock Alert</title>
    <style>
        /* Reset styles */
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            -ms-interpolation-mode: bicubic;
        }

        /* Container */
        .email-wrapper {
            width: 100%;
            background-color: #f8f9fa;
            padding: 20px 0;
        }
        .email-container {
            max-width: 850px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }

        /* Header */
        .email-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .alert-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .header-title {
            font-size: 28px;
            margin: 10px 0;
            font-weight: bold;
        }
        .header-subtitle {
            font-size: 14px;
            margin: 5px 0 0 0;
        }

        /* Body */
        .email-body {
            padding: 30px 20px;
        }
        .lead-text {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .body-text {
            font-size: 14px;
            line-height: 1.6;
            color: #333333;
            margin-bottom: 15px;
        }

        /* Product Card for Mobile */
        .product-card {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #ffffff;
        }
        .product-row {
            margin-bottom: 8px;
            font-size: 14px;
        }
        .product-label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            min-width: 110px;
        }
        .product-value {
            color: #212529;
        }
        .stock-critical {
            color: #dc3545;
            font-weight: bold;
            font-size: 16px;
        }
        .stock-low {
            color: #ffc107;
            font-weight: bold;
            font-size: 16px;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-danger {
            background-color: #dc3545;
            color: #ffffff;
        }
        .badge-warning {
            background-color: #ffc107;
            color:rgb(255, 255, 255);
        }

        /* Alert Box */
        .alert-box {
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .alert-text {
            color: #842029;
            font-size: 14px;
            margin: 0;
        }

        /* Button */
        .button-container {
            text-align: start;
            margin: 25px 0;
        }
        .btn {
            display: inline-block;
            padding: 14px 30px;
            background-color: #dc3545;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
        }

        /* Footer */
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
        }
        .footer-text {
            color: #6c757d;
            font-size: 12px;
            margin: 5px 0;
        }

        /* Desktop Table (Hidden on Mobile) */
        .desktop-table {
            width: 100%;
            margin: 20px 0;
        }
        .desktop-table th {
            background-color: #343a40;
            color: #ffffff;
            padding: 12px 8px;
            text-align: left;
            font-size: 13px;
        }
        .desktop-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 13px;
        }
        .bg-warning-subtle {
            background-color:rgba(255, 193, 7, 0.16) !important;
        }
        .text-warning {
            color: #FFC107 !important;
        }
        .text-white {
            color:rgb(249, 249, 249) !important;
        }

        /* Mobile Responsive */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                border-radius: 0 !important;
            }
            .email-wrapper {
                padding: 0 !important;
            }
            .email-body {
                padding: 20px 15px !important;
            }
            .header-title {
                font-size: 24px !important;
            }
            .desktop-table {
                display: none !important;
            }
            .alert-icon {
                font-size: 36px !important;
            }
            .button-container {
                text-align: center;
            }
        }

        @media only screen and (min-width: 601px) {
            .product-card {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    @php
        if (!function_exists('formatNumber')) {
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
        }
        if (!function_exists('stockType')) {
            function stockType($num, $type = 'kg')
            {
                if ($type === 'kg') {
                    $n = (float) $num;
                    return $n < 1 ? $n > 0 ? 'gm' : 'kg' : 'kg';
                } elseif ($type === 'ft') {
                    $n = (float) $num;
                    return $n < 1 ? $n > 0 ? 'inchi' : 'ft' : 'ft';
                } elseif ($type === 'yard') {
                    $n = (float) $num;
                    return $n < 1 ? $n > 0 ? 'inchi' : 'yard' : 'yard';
                } else {
                    $n = (float) $num;
                    return $n < 1 ? $n > 0 ? 'inchi' : 'm' : 'm';
                }
            }
        }
    @endphp
    <table role="presentation" class="email-wrapper" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" class="email-container" cellpadding="0" cellspacing="0">
                    <!-- Header -->
                    <tr>
                        <td class="email-header">
                            <div class="alert-icon">⚠️</div>
                            <h1 class="header-title">Low Stock Alert</h1>
                            <p class="header-subtitle">Immediate attention required</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="email-body">
                            <p class="lead-text">Dear {{ $user->name }},</p>

                            <p class="body-text">This is an automated notification to inform you that the following products have reached critical or low stock levels and require immediate restocking:</p>

                            <!-- Desktop Table View -->
                            <div class="table-responsive">
                                <table class="desktop-table" cellpadding="0" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Product SKU</th>
                                            <th>Product Name</th>
                                            <th>Item Type</th>
                                            <th>Brand</th>
                                            <th>Category</th>
                                            <th>Stock</th>
                                            {{-- <th>Min Req</th> --}}
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php

                                        $zero_stock_count = $products->filter(function ($product) {
                                            $stock = $product->stock_w_type == 'none' ? $product->stock : $product->stock_w;
                                            return $stock <= 0;
                                        })->count();
                                    @endphp
                                        @if($products->count() > 0)
                                            @foreach($products as $product)
                                                @php
                                                    $stock = $product->stock_w_type == 'none' ? $product->stock : $product->stock_w;
                                                    $stockClass = ($stock > 0 && $stock <= $product->low_stock_level) ? "text-warning" : "stock-critical";
                                                    $stock_in_count = $product->stock_w_type !== 'none' ? formatNumber($product->stock_w, $product->stock_w_type) : (int) $product->stock;
                                                    $weight_type = $product->stock_w_type !== 'none' ? stockType($product->stock_w, $product->stock_w_type) : ' pcs';
                                                @endphp
                                                <tr>
                                                    <td>#{{ $product->sku }}</td>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->item_type?->name ?? 'N/A' }}</td>
                                                    <td>{{ $product->brand?->name ?? 'N/A' }}</td>
                                                    <td>{{ $product->category?->name ?? 'N/A' }}</td>
                                                    <td class="{{ $stockClass }}">
                                                        <div style="width: 50px;">
                                                            {{ $stock_in_count }}
                                                            {{ $weight_type }}
                                                        </div>
                                                    </td>
                                                    {{-- <td>20</td> --}}
                                                    <td>
                                                        <div style="width: 150px;">
                                                            @if($stock == $product->low_stock_level)
                                                                <span class="badge bg-warning-subtle text-warning">Stock Low</span>
                                                            @elseif($stock > 0 && $stock < $product->low_stock_level)
                                                                <span class="badge badge-warning">Stock Very Low</span>
                                                            @else
                                                                <span class="badge badge-danger">Out of Stock</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            @if($products->count())
                                @foreach($products as $product)
                                    @php
                                        $stock = $product->stock_w_type == 'none' ? $product->stock : $product->stock_w;
                                        $stockClass = ($stock > 0 && $stock <= $product->low_stock_level) ? "text-warning" : "stock-critical";
                                        $stock_in_count = $product->stock_w_type !== 'none' ? formatNumber($product->stock_w, $product->stock_w_type) : (int) $product->stock;
                                        $weight_type = $product->stock_w_type !== 'none' ? stockType($product->stock_w, $product->stock_w_type) : ' pcs';
                                    @endphp
                                    <div class="product-card">
                                        <div class="product-row"><span class="product-label">Product SKU:</span> <span class="product-value">{{ $product->sku }}</span></div>
                                        <div class="product-row"><span class="product-label">Product Name:</span> <span class="product-value">{{ $product->name }}</span></div>
                                        <div class="product-row"><span class="product-label">Category:</span> <span class="product-value">{{ $product->item_type?->name ?? 'N/A' }}</span></div>
                                        <div class="product-row"><span class="product-label">Category:</span> <span class="product-value">{{ $product->brand?->name ?? 'N/A' }}</span></div>
                                        <div class="product-row"><span class="product-label">Category:</span> <span class="product-value">{{ $product->category?->name ?? 'N/A' }}</span></div>
                                        <div class="product-row">
                                            <span class="product-label">Current Stock:</span> <span class="{{ $stockClass }}">
                                                {{ $stock_in_count }}
                                                {{ $weight_type }}
                                            </span>
                                        </div>
                                        <div class="product-row">
                                            <span class="product-label">Status:</span> @if($stock == $product->low_stock_level)
                                                        <span class="badge bg-warning-subtle text-warning">Stock Low</span>
                                                    @elseif($stock > 0 && $stock < $product->low_stock_level)
                                                        <span class="badge badge-warning">Stock Very Low</span>
                                                    @else
                                                        <span class="badge badge-danger">Out of Stock</span>
                                                    @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Summary Alert -->
                            <div class="alert-box">
                                <p class="alert-text"><strong>Summary:</strong> {{ $zero_stock_count }} products at critical level (0 stock).</p>
                            </div>

                            <!-- Action Button -->
                            <div class="button-container">
                                <a class="btn text-white">Reorder Products Now</a>
                            </div>

                            <p class="body-text">Please take immediate action to prevent stock-outs and maintain optimal inventory levels.</p>

                            <p class="body-text">Best regards,<br><strong>{{ config('app.name') }}</strong></p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td class="email-footer">
                            <p class="footer-text">This is an automated email. Please do not reply.</p>
                            <p class="footer-text">© 2025 Your Company Name. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
