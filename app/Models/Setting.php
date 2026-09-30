<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get a setting by key with an optional default.
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value, ?string $group = null): static
    {
        $attributes = ['key' => $key];
        $values = ['value' => $value];
        if ($group !== null) {
            $values['group'] = $group;
        }

        return static::updateOrCreate($attributes, $values);
    }

    /**
     * Return all settings as a key-value associative array.
     */
    public static function getAll(): array
    {
        return static::pluck('value', 'key')->toArray();
    }
}
