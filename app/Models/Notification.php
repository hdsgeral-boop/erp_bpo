<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'type',
        'priority',
        'category',
        'module',
        'event_type',
        'title',
        'message',
        'entity_type',
        'entity_id',
        'action_url',
        'created_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function recipients()
    {
        return $this->hasMany(NotificationRecipient::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
