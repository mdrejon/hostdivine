<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
    ];

    /**
     * Cache key for settings
     */
    public const CACHE_KEY = 'app_global_settings';

    /**
     * Container key holding the settings loaded during the current request.
     */
    protected const MEMO_KEY = 'settings.loaded';

    /**
     * Retrieve a setting value by key with fallback default
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::getAllCached();

        return $all[$key] ?? $default;
    }

    /**
     * Retrieve a JSON setting (menus, footer, widgets) as an array.
     *
     * @return array<mixed>
     */
    public static function json(string $key): array
    {
        $value = self::get($key);

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Store or update a setting
     */
    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): self
    {
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $type = 'json';
        }

        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'type' => $type]
        );

        self::clearCache();

        return $setting;
    }

    /**
     * Retrieve all settings mapped as key => value from cache
     *
     * @return array<string, mixed>
     */
    public static function getAllCached(): array
    {
        if (! app()->bound(self::MEMO_KEY)) {
            app()->instance(self::MEMO_KEY, Cache::rememberForever(self::CACHE_KEY, function () {
                return self::query()->pluck('value', 'key')->toArray();
            }));
        }

        return app(self::MEMO_KEY);
    }

    /**
     * Invalidate the cache
     */
    public static function clearCache(): void
    {
        app()->forgetInstance(self::MEMO_KEY);
        Cache::forget(self::CACHE_KEY);
    }
}
