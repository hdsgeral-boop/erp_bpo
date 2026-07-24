<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class, 'plan_id');
    }
}
