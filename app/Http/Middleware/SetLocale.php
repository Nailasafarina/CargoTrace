<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request and apply localization.
     * Priority: users.locale (if authenticated) -> session -> Accept-Language header (API) -> default id.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // 1. Authenticated user preference
        if ($request->user() && ! empty($request->user()->locale)) {
            $locale = $request->user()->locale;
        }
        // 2. Session preference (if session is initialized)
        elseif ($request->hasSession() && $request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }
        // 3. Persistent cookie preference (fallback)
        elseif ($request->hasCookie('locale') && in_array($request->cookie('locale'), ['id', 'en'], true)) {
            $locale = $request->cookie('locale');
            if ($request->hasSession()) {
                $request->session()->put('locale', $locale);
            }
        }
        // 4. Accept-Language header (API clients or browser request)
        elseif ($header = $request->header('Accept-Language')) {
            $parts = explode(',', $header);
            foreach ($parts as $part) {
                $code = substr(strtolower(trim(explode(';', $part)[0])), 0, 2);
                if (in_array($code, ['id', 'en'], true)) {
                    $locale = $code;
                    break;
                }
            }

            if (! $locale) {
                $preferred = $request->getPreferredLanguage(['id', 'en']);
                if ($preferred) {
                    $locale = $preferred;
                }
            }
        }

        // 4. Fallback to default 'id' (or configured locale)
        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = config('app.locale', 'id');
            if (! in_array($locale, ['id', 'en'], true)) {
                $locale = 'id';
            }
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
