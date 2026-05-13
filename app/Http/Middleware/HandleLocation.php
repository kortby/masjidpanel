<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleLocation
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('location')) {
            $location = $request->query('location');
            if (empty($location)) {
                $request->session()->forget('location');
            } else {
                $request->session()->put('location', $location);
            }
        }

        return $next($request);
    }
}
