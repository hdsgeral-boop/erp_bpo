<?php

namespace App\Models;

use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasAttachments;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'is_master_data' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function convertedToOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'converted_to_order_id');
    }

    public function items()
    {
        return $this->morphMany(PurchaseItem::class, 'parent');
    }
}
