<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'product_id',
        'sku',
        'name',
        'attributes_json',
        'unit_price',
        'unit_cost',
        'stock_qty',
        'is_active',
    ];

    protected $casts = [
        'attributes_json' => 'array',
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'stock_qty' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
