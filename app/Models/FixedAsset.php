<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class FixedAsset extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable, \App\Traits\HasAttachments;

    protected $fillable = [
        'company_id',
        'category_id',
        'code',
        'name',
        'purchase_date',
        'purchase_value',
        'residual_value',
        'useful_life_years',
        'status',
        'vendor_id',
        'department_id',
        'employee_id',
        'location',
        'notes'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_value' => 'decimal:2',
        'residual_value' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class);
    }

    public function vendor()
    {
        return $this->belongsTo(ThirdParty::class, 'vendor_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function movements()
    {
        return $this->hasMany(AssetMovement::class)->orderBy('movement_date', 'desc')->orderBy('created_at', 'desc');
    }

    public function depreciations()
    {
        return $this->hasMany(AssetDepreciation::class);
    }
}
