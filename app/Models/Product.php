<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Traits\HasAttachments;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->code)) {
                $product->code = static::generateNextCode($product->company_id, $product->category_id);
            }
        });
    }

    public static function generateNextCode($companyId, $categoryId = null): string
    {
        $companyId = $companyId ?? (session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1));
        $catPrefix = 'PRD';
        if ($categoryId) {
            $category = ProductCategory::find($categoryId);
            if ($category && !empty($category->code)) {
                $cleanCat = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $category->code));
                if (!empty($cleanCat)) {
                    $catPrefix = substr($cleanCat, 0, 4);
                }
            }
        }

        $nextNum = static::where('company_id', $companyId)->count() + 1;
        $code = sprintf("PRD-%s-%04d", $catPrefix, $nextNum);

        while (static::where('company_id', $companyId)->where('code', $code)->exists()) {
            $nextNum++;
            $code = sprintf("PRD-%s-%04d", $catPrefix, $nextNum);
        }

        return $code;
    }

    public function getPriceAttribute()
    {
        return $this->attributes['unit_price'] ?? 0;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value;
    }

    public function getIsActiveAttribute(): bool
    {
        return !($this->attributes['is_blocked'] ?? false);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function stocks()
    {
        return $this->hasMany(WarehouseStock::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class)->orderBy('movement_date', 'desc')->orderBy('created_at', 'desc');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function kitComponents()
    {
        return $this->hasMany(ProductKitComponent::class, 'parent_product_id');
    }
}
