<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackDeviceCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $deviceId = $request->cookie('device_id');

        if (! $deviceId) {
            $deviceId = \Illuminate\Support\Str::uuid()->toString();
            // Attach to request so it's available in the current lifecycle
            $request->cookies->set('device_id', $deviceId);
            
            $response = $next($request);
            
            // Set long-lived cookie (10 years)
            return $response->withCookie(cookie('device_id', $deviceId, 5256000));
        }

        return $next($request);
    }
}
