<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Traits\HasAttachments;

    protected $guarded = [];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'stock_qty' => 'integer',
        'is_inventory' => 'boolean',
        'is_asset' => 'boolean',
        'is_blocked' => 'boolean',
        'is_master_data' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (isset($product->unit_price)) {
                $product->price = $product->unit_price;
            } elseif (isset($product->price)) {
                $product->unit_price = $product->price;
            }
        });
    }

    public function getPriceAttribute()
    {
        return $this->attributes['unit_price'] ?? ($this->attributes['price'] ?? 0);
    }

    public function getIsActiveAttribute(): bool
    {
        return !($this->attributes['is_blocked'] ?? false);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function stocks()
    {
        return $this->hasMany(WarehouseStock::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class)->orderBy('movement_date', 'desc')->orderBy('created_at', 'desc');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
}
