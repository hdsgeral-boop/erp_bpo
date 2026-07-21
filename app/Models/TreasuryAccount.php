<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreasuryAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'currency',
        'initial_balance',
        'current_balance',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
