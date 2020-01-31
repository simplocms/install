<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!auth()->check() || !auth()->user()->enabled){
            if ($request->ajax()) {
                return response()->json([
                    'renew_token' => csrf_token()
                ], 401);
            }
            return redirect()->route('admin.auth.login');
        }

        return $next($request);
    }
}
