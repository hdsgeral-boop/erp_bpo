<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAgent extends Model
{
    protected $fillable = [
        'company_id',
        'ai_provider_id',
        'ai_model_id',
        'name',
        'description',
        'system_prompt',
        'temperature',
        'is_active',
    ];

    public function provider()
    {
        return $this->belongsTo(AiProvider::class, 'ai_provider_id');
    }

    public function aiModel()
    {
        return $this->belongsTo(AiModel::class, 'ai_model_id');
    }

    public function conversations()
    {
        return $this->hasMany(AiConversation::class);
    }

    public function tools()
    {
        return $this->hasMany(AiAgentTool::class);
    }

    public function knowledgeBases()
    {
        return $this->hasMany(AiKnowledgeBase::class);
    }
}
