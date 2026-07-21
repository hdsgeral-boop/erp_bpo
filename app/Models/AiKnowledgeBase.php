<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiKnowledgeBase extends Model
{
    protected $fillable = [
        'ai_agent_id',
        'document_path',
        'status',
    ];

    public function agent()
    {
        return $this->belongsTo(AiAgent::class, 'ai_agent_id');
    }
}
