<?php


namespace App\Helper\Setting;


use Illuminate\Support\Facades\Cache;

class Tax
{
    private static $cacheKey = 'tax';

    public static function update($tax)
    {
        Cache::put(self::$cacheKey, $tax);
    }

    public static function fetch()
    {
        return [self::$cacheKey => Cache::get(self::$cacheKey)];
    }
}
