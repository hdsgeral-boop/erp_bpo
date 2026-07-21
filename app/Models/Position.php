<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'code',
        'title',
        'description',
        'department_id',
        'is_management'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
