<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['key', 'value'];

    /**
     * 获取设置值，支持默认值
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);

        return $setting ? $setting->value : $default;
    }

    /**
     * 设置值
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
