<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'is_posted' => 'boolean',
        'is_master_data' => 'boolean',
        'total_amount' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function thirdParty()
    {
        return $this->belongsTo(ThirdParty::class, 'third_party_id');
    }

    public function treasuryAccount()
    {
        return $this->belongsTo(TreasuryAccount::class);
    }

    public function items()
    {
        return $this->hasMany(ReceiptItem::class);
    }
}
