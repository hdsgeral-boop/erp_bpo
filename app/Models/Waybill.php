<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waybill extends Model
{
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(ThirdParty::class, 'customer_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(WaybillItem::class);
    }
}
