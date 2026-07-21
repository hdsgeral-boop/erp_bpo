<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group'
    ];

    /**
     * Get a setting value, using cache.
     */
    public static function getVal($key, $default = null)
    {
        $settings = \Illuminate\Support\Facades\Cache::rememberForever('system_settings', function () {
            return self::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Clear the cache.
     */
    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('system_settings');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('system_settings');
        });
    }
}
