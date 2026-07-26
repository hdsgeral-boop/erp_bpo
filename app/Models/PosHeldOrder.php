<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosHeldOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'pos_session_id',
        'user_id',
        'customer_id',
        'reference_name',
        'items_json',
        'totals_json',
        'status',
    ];

    protected $casts = [
        'items_json' => 'array',
        'totals_json' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function posSession()
    {
        return $this->belongsTo(PosSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(ThirdParty::class, 'customer_id');
    }
}
