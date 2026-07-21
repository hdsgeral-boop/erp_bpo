<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Traits\HasAttachments;

    protected $guarded = [];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'stock_qty' => 'decimal:2',
        'is_inventory' => 'boolean',
    ];

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
