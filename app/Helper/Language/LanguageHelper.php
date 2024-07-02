<?php


namespace App\Helper\Language;


use App\Helper\Response\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LanguageHelper
{
    private static $defaultLanguageKey = 'defaultLanguage';
    private static $allLanguageKey = 'languages';
    private static $headerKey = 'Accept-Language';

    public static function getHeaderKey()
    {
        return self::$headerKey;
    }

    public static function getCacheDefaultKey()
    {
        return self::$defaultLanguageKey;
    }

    public static function getCacheAllKey()
    {
        return self::$allLanguageKey;
    }

    public static function getAppLanguage(Request $request)
    {
        return $request->header(self::$headerKey) ? $request->header(self::$headerKey) : 'de';
    }

    public static function setCacheDefaultLang($languageCode)
    {
        Cache::put(self::$defaultLanguageKey, $languageCode);
    }

    public static function getCacheDefaultLang()
    {
        return Cache::get(self::$defaultLanguageKey);
    }

    public static function setCacheAllLang($languages)
    {

        Cache::put(self::$allLanguageKey, $languages);
    }

    public static function getCacheAllLang()
    {
        return Cache::get(self::$allLanguageKey);
    }

    public static function languageExist($lang_code)
    {
        return isset(Cache::get(self::$allLanguageKey)[$lang_code]);
    }
}
