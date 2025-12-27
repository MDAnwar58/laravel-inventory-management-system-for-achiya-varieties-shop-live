<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'item_type_id',
        'brand_id',
        'category_id',
        'sub_category_id',
        'sku',
        'barcode',
        'name',
        'slug',
        'price',
        'discount_price',
        'retail_price',
        'retail_price_discount',
        'cost_price',
        'stock',
        'stock_w',
        'stock_w_type',
        'low_stock_level',
        'purchase_limit',
        'desc',
        'image',
        'status',
        'stock_updated',
        'stock_updated_at',
        'change_price',
        'change_price_updated_at',
        'sold_units',
        'solded_at',
        'meta_title',
        'meta_desc',
        'meta_keywords',
    ];
    protected $casts = [
        'stock_updated_at' => 'datetime',
        'change_price_updated_at' => 'datetime',
        'solded_at' => 'datetime',
    ];


    public function item_type()
    {
        return $this->belongsTo(ItemType::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class);
    }
    public function sales_orders_products()
    {
        return $this->hasMany(SalesOrderProduct::class);
    }
}
