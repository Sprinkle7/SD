<?php

namespace App\Http\Middleware\User;

use App\Helper\Language\LanguageHelper;
use App\Helper\Response\Response;
use App\Helper\SystemMessage\SystemMessage;
use Closure;
use Illuminate\Http\Request;

class IsUser
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
        $message = new SystemMessage(LanguageHelper::getAppLanguage($request),
            'user', LanguageHelper::getCacheDefaultLang());
        $role = $request->user()->role;
        if ($role->id != 3) {
            return Response::error401($message->error401());
        }
        return $next($request);
    }
}
