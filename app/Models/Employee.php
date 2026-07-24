<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Employee extends Model implements AuditableContract
{
    use Auditable;
    use \App\Traits\HasAttachments;

    protected $guarded = [];

    protected $casts = [
        'admission_date' => 'date',
        'is_active' => 'boolean',
        'is_retired' => 'boolean',
        'is_master_data' => 'boolean',
        'base_salary' => 'decimal:2',
        'subsidy_meal' => 'decimal:2',
        'subsidy_transport' => 'decimal:2',
        'nif' => 'encrypted',
        'iban' => 'encrypted',
        'inss' => 'encrypted',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}

