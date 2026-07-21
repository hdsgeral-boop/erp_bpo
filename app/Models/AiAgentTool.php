<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAgentTool extends Model
{
    protected $fillable = [
        'ai_agent_id',
        'tool_class',
    ];

    public function agent()
    {
        return $this->belongsTo(AiAgent::class, 'ai_agent_id');
    }
}
