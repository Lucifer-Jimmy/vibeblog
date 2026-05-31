<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * 获取或设置站点配置
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}
