<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AiProvider extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'driver',
        'base_url',
        'api_key',
        'priority',
        'fallback_id',
        'temperature',
        'max_tokens',
        'timeout',
        'stream',
        'is_active',
    ];

    protected $hidden = [
        'api_key',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
        'is_active' => 'boolean',
        'stream' => 'boolean',
    ];

    public function agents()
    {
        return $this->hasMany(AiAgent::class);
    }

    public function models()
    {
        return $this->hasMany(AiModel::class, 'ai_provider_id');
    }

    public function fallback()
    {
        return $this->belongsTo(AiProvider::class, 'fallback_id');
    }
}
