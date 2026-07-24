<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->code)) {
                $category->code = static::generateNextCode($category->company_id, $category->name);
            }
        });
    }

    public static function generateNextCode($companyId, $name = null): string
    {
        $companyId = $companyId ?? (session('company_id') ?? (auth()->check() ? auth()->user()->company_id : 1));
        $prefix = 'CAT';
        if (!empty($name)) {
            $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: $name));
            if (strlen($clean) >= 3) {
                $prefix = 'CAT-' . substr($clean, 0, 3);
            }
        }

        $count = static::where('company_id', $companyId)->count() + 1;
        $code = sprintf("%s-%03d", $prefix, $count);

        while (static::where('company_id', $companyId)->where('code', $code)->exists()) {
            $count++;
            $code = sprintf("%s-%03d", $prefix, $count);
        }

        return $code;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
