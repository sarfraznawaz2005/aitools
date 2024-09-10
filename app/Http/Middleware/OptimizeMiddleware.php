<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        //$response->headers->set('Cache-Control', 'public, max-age=2592000', true);
        //$response->headers->add(['Cache-Control' => 'public, max-age=2592000']);

        return $response;
    }
}
