<?php

use App\Models\Setting;

/**
 * Get a setting from the settings table.
 * Automatically JSON-decodes arrays/objects.
 */
if (! function_exists('setting')) {
    function setting(string $key, $default = null) {
        $row = Setting::where('key', $key)->first();
        if (! $row) return $default;

        $val = $row->value;
        $decoded = json_decode($val, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $val;
    }
}

/**
 * Set/update a setting.
 */
if (! function_exists('setting_set')) {
    function setting_set(string $key, $value): void {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value]
        );
    }
}
