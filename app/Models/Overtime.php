<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Overtime extends Model implements AuditableContract
{
    use Auditable;

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}

