<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        $acceptLanguage = $request->header('Accept-Language');
        $preferredLanguage = Str::before($acceptLanguage, ',');
        $preferredLanguage = Str::before($preferredLanguage, '-');
        $supportedLocales = config('app.locales');
        $defaultLocale = config('app.locale');
        $locale = $this->validateLocale($preferredLanguage, $supportedLocales, $defaultLocale);
        App::setLocale($locale);

        return $next($request);
    }

    protected function validateLocale($preferredLanguage, array $supportedLocales, $defaultLocale)
    {
        if (empty($preferredLanguage) || !in_array($preferredLanguage, $supportedLocales)) {
            return $defaultLocale;
        }

        return $preferredLanguage;
    }
}
