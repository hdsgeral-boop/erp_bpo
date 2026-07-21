<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reconciliation extends Model
{
    protected $guarded = [];

    public function bankStatementLines()
    {
        return $this->hasMany(\App\Models\BankStatementLine::class);
    }

    public function journalLines()
    {
        return $this->hasMany(\App\Models\JournalLine::class);
    }
}
