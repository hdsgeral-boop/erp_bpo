<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_provider_id',
        'name',
        'identifier',
        'supports_chat',
        'supports_streaming',
        'supports_vision',
        'supports_function_calling',
        'supports_tool_calling',
        'supports_embeddings',
        'supports_json_mode',
        'context_window',
        'max_tokens',
        'max_temperature',
        'is_active',
    ];

    protected $casts = [
        'supports_chat' => 'boolean',
        'supports_streaming' => 'boolean',
        'supports_vision' => 'boolean',
        'supports_function_calling' => 'boolean',
        'supports_tool_calling' => 'boolean',
        'supports_embeddings' => 'boolean',
        'supports_json_mode' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function provider()
    {
        return $this->belongsTo(AiProvider::class, 'ai_provider_id');
    }

    public function agents()
    {
        return $this->hasMany(AiAgent::class, 'ai_model_id');
    }
}
