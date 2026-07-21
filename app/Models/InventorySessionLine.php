<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventorySessionLine extends Model
{
    protected $guarded = [];

    public function session()
    {
        return $this->belongsTo(InventorySession::class, 'inventory_session_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
