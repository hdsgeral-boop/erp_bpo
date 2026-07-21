<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'received_qty' => 'decimal:2',
        'is_master_data' => 'boolean',
    ];

    /**
     * Get the parent document (PurchaseRequest, PurchaseOrder, etc.)
     */
    public function parent()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }
}
