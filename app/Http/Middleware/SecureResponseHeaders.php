<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureResponseHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->header('Cache-Control', 'no-store');
        $response->header('Content-Security-Policy', "frame-ancestors 'none'");
        $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubdomains');
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-Frame-Options', 'DENY');

        return $response;
    }
}
