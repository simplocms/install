<?php

namespace App\Http\Middleware;

use App\Services\ModelPrefetch\ModelPrefetch;
use Closure;

class PrefetchModels
{
    /**
     * Verify language code in url.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $url = $request->fullUrl();
        $prefetch = resolve(ModelPrefetch::class);

        // request
        $prefetch->startRecording();
        $prefetch->prefetch($url);

        // response
        $response = $next($request);

        $prefetch->stopRecording();
        $prefetch->updateCache($url);

        return $response;
    }
}
