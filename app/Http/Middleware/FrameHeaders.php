<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FrameHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     $response = $next($request);
    //     $response->headers->set('X-Frame-Options', 'ALLOW-FROM http://192.168.11.105'); // Replace with your iframe source
    //     return $response;
    // }
}