<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Auth;


class IsRhOrResp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if ($this->passesIsRHmd($request) || $this->passesIsRespmd($request)) {
            return $next($request);
        }

        return redirect()->back();
    }

    protected function passesIsRHmd($request)
    {
        // Logic for IsRHmd middleware
        // Return true if it passes, otherwise false
        return Auth::user()->isRH();
    }

    protected function passesIsRespmd($request)
    {
        // Logic for IsRespmd middleware
        // Return true if it passes, otherwise false
        return Auth::user()->isResponsable();
    }
}
