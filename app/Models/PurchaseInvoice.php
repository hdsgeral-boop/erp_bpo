<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $guarded = [];

    public function supplier()
    {
        return $this->belongsTo(\App\Models\ThirdParty::class, 'supplier_id');
    }

    public function items()
    {
        return $this->morphMany(\App\Models\PurchaseItem::class, 'parent');
    }
}
