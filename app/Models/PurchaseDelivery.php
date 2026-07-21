<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;

class PurchaseDelivery extends Model
{
    use HasAttachments;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'is_posted' => 'boolean',
        'is_validated' => 'boolean',
        'is_master_data' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'order_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->morphMany(PurchaseItem::class, 'parent');
    }
}
