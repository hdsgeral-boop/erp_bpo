<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $guarded = [];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
