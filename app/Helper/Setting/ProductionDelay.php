<?php


namespace App\Helper\Setting;


use Illuminate\Support\Facades\Cache;

class ProductionDelay
{
    private static $cacheKey = 'production_delay';

    public static function update($delay)
    {
        Cache::put(self::$cacheKey, $delay);
    }

    public static function fetch()
    {
        return [self::$cacheKey => Cache::get(self::$cacheKey)];
    }
}
