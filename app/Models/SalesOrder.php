<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'order_number',
        'status',
        'payment_status',
        'sub_total',
        'total',
        'tax',
        'paid_amount',
        'due_amount',
        'order_date',
        'due_date',
        'cancelled_date',
        'currency',
        'invoice_number',
        'notes',
        'discount_percent',
        'memo_no',
    ];
    protected $casts = [
        'order_date' => 'datetime',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales_order_products()
    {
        return $this->hasMany(SalesOrderProduct::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'sales_order_products')
            ->withPivot(['price', 'qty', 'total', 'discount_percent', 'tax']);
    }
}
