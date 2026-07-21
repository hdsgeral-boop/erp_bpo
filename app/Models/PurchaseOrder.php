<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasAttachments;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'is_master_data' => 'boolean',
        'total_amount' => 'decimal:2',
        'total_tax' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier()
    {
        return $this->belongsTo(ThirdParty::class, 'supplier_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function deliveries()
    {
        return $this->hasMany(PurchaseDelivery::class, 'order_id');
    }

    public function items()
    {
        return $this->morphMany(PurchaseItem::class, 'parent');
    }
}
