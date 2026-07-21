<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $guarded = [];

    public function lines()
    {
        return $this->hasMany(\App\Models\JournalLine::class);
    }
}
