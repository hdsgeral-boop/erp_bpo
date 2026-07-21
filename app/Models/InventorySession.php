<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventorySession extends Model
{
    protected $guarded = [];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function lines()
    {
        return $this->hasMany(InventorySessionLine::class, 'inventory_session_id');
    }
}
