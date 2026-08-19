<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'label',
        'type',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('settings');
        });

        static::deleted(function () {
            Cache::forget('settings');
        });
    }

    public static function allCached(): array
    {
        return Cache::rememberForever('settings', function () {
            return self::pluck('value', 'key')->toArray();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = self::allCached();

        return $settings[$key] ?? $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', ?string $label = null): void
    {
        self::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'label' => $label ?? $key]
        );
    }

    public static function setMany(array $data, string $group = 'general'): void
    {
        foreach ($data as $key => $value) {
            self::set($key, $value, $group);
        }
    }
}