<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $guarded = [];

    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->attributes['description'] ?? '';
    }
}
