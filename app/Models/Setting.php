<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key','value'];
    public $timestamps = true;

    // read all settings once
    public static function allAsArray(): array {
        return Cache::rememberForever('settings.all', function () {
            return self::query()->pluck('value','key')->toArray();
        });
    }

    public static function get(string $key, $default = null) {
        $all = self::allAsArray();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function set(string $key, $value): void {
        self::updateOrCreate(['key'=>$key], ['value'=>$value]);
        Cache::forget('settings.all');
    }
}
