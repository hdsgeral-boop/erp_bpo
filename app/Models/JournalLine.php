<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalLine extends Model
{
    protected $guarded = [];

    public function journal()
    {
        return $this->belongsTo(\App\Models\Journal::class, 'journal_id');
    }

    public function thirdParty()
    {
        return $this->belongsTo(\App\Models\ThirdParty::class, 'third_party_id');
    }
}
