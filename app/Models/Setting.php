<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label'];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return $default;
        return static::castValue($setting->value, $setting->type);
    }

    public static function set(string $key, $value): void
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) return;
        $setting->update(['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]);
    }

    public static function theme(): array
    {
        $defaults = [
            'theme_bg_page' => '#f1f5f9',
            'theme_bg_card' => '#ffffff',
            'theme_text_primary' => '#1e3a8a',
            'theme_accent' => '#2563eb',
            'theme_sidebar_bg' => '#111827',
        ];
        return Cache::remember('mr_theme', 3600, function () use ($defaults) {
            try {
                $rows = static::whereIn('key', array_keys($defaults))->pluck('value', 'key')->toArray();
                return array_merge($defaults, array_filter($rows));
            } catch (\Throwable $e) {
                return $defaults;
            }
        });
    }

    public static function clearThemeCache(): void
    {
        Cache::forget('mr_theme');
    }

    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'bool' => (bool) $value,
            'int' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
