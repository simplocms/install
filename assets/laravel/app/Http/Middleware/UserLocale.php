<?php

namespace App\Http\Middleware;

use Closure;

class UserLocale
{
    /**
     * Activate locale for authorized user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $locale = auth()->user()->locale;

        if (!in_array($locale, config('app.enabled_locales'))) {
            return $next($request);
        }

        app()->setLocale($locale);
        return $next($request);
    }
}
