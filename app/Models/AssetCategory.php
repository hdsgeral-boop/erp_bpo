<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class AssetCategory extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'company_id',
        'name',
        'depreciation_rate',
        'account_asset',
        'account_depreciation',
        'account_expense'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assets()
    {
        return $this->hasMany(FixedAsset::class, 'category_id');
    }
}
