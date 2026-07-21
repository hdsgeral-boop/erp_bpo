<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThirdParty extends Model
{
    use \App\Traits\HasAttachments;

    protected $guarded = [];

    protected $casts = [
        'is_customer' => 'boolean',
        'is_supplier' => 'boolean',
        'is_master_data' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function purchases()
    {
        return $this->hasMany(PurchaseInvoice::class, 'supplier_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'third_party_id');
    }

    public function scopeCustomers($query)
    {
        return $query->where('is_customer', true);
    }

    public function scopeSuppliers($query)
    {
        return $query->where('is_supplier', true);
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
