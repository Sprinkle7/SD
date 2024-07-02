<?php

namespace App\Http\Middleware\Language;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Models\Language\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LanguageHandler
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (!Cache::has(LanguageHelper::getCacheAllKey())) {
            Language::cacheLanguages();
        }
        if (!Cache::has(LanguageHelper::getCacheDefaultKey())) {
            $lang = Language::where('default', 1)->first();
            Language::cacheDefaultLanguage($lang['code']);
        }
        app()->setLocale($request->header(LanguageHelper::getHeaderKey()));
        if (!$request->hasHeader(LanguageHelper::getHeaderKey())
            || !LanguageHelper::languageExist($request->header(LanguageHelper::getHeaderKey()))) {

            $request->headers->set(LanguageHelper::getHeaderKey(), LanguageHelper::getCacheDefaultLang());
            app()->setLocale($request->header(LanguageHelper::getHeaderKey()));
        }
        return $next($request);
    }
}
