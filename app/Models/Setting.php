<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Retrieve a setting value by key, with fallback to config or default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $record = static::where('key', $key)->first();

            if (! $record) {
                return $default;
            }

            return match ($record->type) {
                'integer', 'int' => (int) $record->value,
                'boolean', 'bool' => filter_var($record->value, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($record->value, true),
                default => $record->value,
            };
        });
    }

    /**
     * Store or update a setting value by key.
     */
    public static function set(string $key, mixed $value, string $type = 'string', ?string $description = null): static
    {
        $serializedValue = is_array($value) ? json_encode($value) : (string) $value;

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $serializedValue,
                'type' => $type,
                'description' => $description,
            ]
        );

        Cache::forget("setting.{$key}");

        return $setting;
    }
}
