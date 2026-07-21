<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAttachments;

class Sale extends Model
{
    use HasAttachments;

    protected $fillable = [
        'company_id', 'customer_id', 'doc_type', 'doc_number', 'date',
        'status', 'is_posted', 'related_doc_id', 'is_master_data',
        'warehouse_id', 'created_by', 'cancelled_by', 'cancelled_at', 'cancellation_reason',
        'total_amount', 'total_tax', 'total_discount', 'notes',
        'amount_paid', 'payment_status'
    ];

    protected $casts = [
        'date' => 'date',
        'is_posted' => 'boolean',
        'is_master_data' => 'boolean',
        'total_amount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(ThirdParty::class, 'customer_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }
}
