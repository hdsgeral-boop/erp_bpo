<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'third_party_id',
        'sale_id',
        'type',
        'points',
        'amount_kwanza',
        'description',
    ];

    protected $casts = [
        'points' => 'integer',
        'amount_kwanza' => 'decimal:2',
    ];

    public function thirdParty()
    {
        return $this->belongsTo(ThirdParty::class, 'third_party_id');
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
