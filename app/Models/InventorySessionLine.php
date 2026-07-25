<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventorySessionLine extends Model
{
    protected $guarded = [];

    public function getSystemQtyAttribute()
    {
        return $this->attributes['system_qty'] ?? $this->attributes['system_quantity'] ?? 0;
    }

    public function getCountedQtyAttribute()
    {
        return $this->attributes['counted_qty'] ?? $this->attributes['counted_quantity'] ?? null;
    }

    public function session()
    {
        return $this->belongsTo(InventorySession::class, 'inventory_session_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
