<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiConversation extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'ai_agent_id',
        'title',
    ];

    public function agent()
    {
        return $this->belongsTo(AiAgent::class, 'ai_agent_id');
    }

    public function messages()
    {
        return $this->hasMany(AiMessage::class);
    }
}
