<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaybillItem extends Model
{
    protected $guarded = [];

    public function waybill()
    {
        return $this->belongsTo(Waybill::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
