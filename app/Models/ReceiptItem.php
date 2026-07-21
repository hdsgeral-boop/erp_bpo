<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'is_master_data' => 'boolean',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }
}
