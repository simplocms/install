<?php

namespace App\Http\Middleware;

use App\Structures\Enums\SingletonEnum;
use Closure;

class ManageResponse
{
    /**
     * Manage the response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        $response->setPublic();
        $maxAge = $response->getMaxAge();

        if (!$maxAge || $maxAge < 0) {
            $response->setMaxAge(3600);
        }

        return SingletonEnum::responseManager()->handle($response);
    }
}
