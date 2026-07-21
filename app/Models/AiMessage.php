<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiMessage extends Model
{
    protected $fillable = [
        'ai_conversation_id',
        'role',
        'content',
        'tool_calls',
        'tool_call_id',
        'tokens_used',
        'cost',
        'meta',
    ];

    protected $casts = [
        'tool_calls' => 'array',
        'meta' => 'array',
    ];

    public function conversation()
    {
        return $this->belongsTo(AiConversation::class, 'ai_conversation_id');
    }
}
