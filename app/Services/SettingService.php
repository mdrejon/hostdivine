<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    /**
     * Get a setting by key
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    /**
     * Set a setting value
     */
    public function set(string $key, mixed $value, string $group = 'general'): Setting
    {
        return Setting::set($key, $value, $group);
    }

    /**
     * Get all settings
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return Setting::getAllCached();
    }

    /**
     * Clear cached settings
     */
    public function clearCache(): void
    {
        Setting::clearCache();
    }
}
