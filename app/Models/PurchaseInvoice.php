<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier()
    {
        return $this->belongsTo(ThirdParty::class, 'supplier_id');
    }

    public function items()
    {
        return $this->morphMany(PurchaseItem::class, 'parent');
    }
}
