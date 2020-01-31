<?php

namespace App\Http\Middleware;

use App\Services\FrontWebTools\FrontWebTools;
use Closure;

class InjectFrontWebTools
{
    /**
     * Verify language code in url
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        $tools = new FrontWebTools();
        $tools->modifyResponse($request, $response);

        return $response;
    }
}
