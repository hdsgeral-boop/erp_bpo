<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMovement extends Model
{

    protected $guarded = [];

    protected $casts = [
        'movement_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(FixedAsset::class, 'fixed_asset_id');
    }

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function fromEmployee()
    {
        return $this->belongsTo(Employee::class, 'from_employee_id');
    }

    public function toEmployee()
    {
        return $this->belongsTo(Employee::class, 'to_employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
