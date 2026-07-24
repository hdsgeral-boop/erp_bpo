<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSession extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function posRegister()
    {
        return $this->belongsTo(PosRegister::class, 'pos_register_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
