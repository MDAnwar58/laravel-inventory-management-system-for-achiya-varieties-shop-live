<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrderProduct extends Model
{
    protected $fillable = [
        'sales_order_id',
        'product_id',
        'user_id',
        'customer_id',
        'price',
        'discount_price',
        'sub_total',
        'total',
        'qty',
        'discount_percent',
        'retail_price_status',
        'stock_w_type',
        'tax',
    ];

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
