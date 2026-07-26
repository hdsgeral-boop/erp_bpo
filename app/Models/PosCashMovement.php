<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosCashMovement extends Model
{
    protected $fillable = [
        'company_id',
        'pos_session_id',
        'user_id',
        'type',
        'amount',
        'reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function session()
    {
        return $this->belongsTo(PosSession::class, 'pos_session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
