<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class AssetDepreciation extends Model implements Auditable
{
    use SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'fixed_asset_id',
        'year',
        'month',
        'depreciation_amount',
        'accumulated_amount',
        'net_book_value',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class);
    }
}
