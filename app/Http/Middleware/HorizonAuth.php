<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HorizonAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array($request->ip(), ['127.0.0.1' /* other allowed IPs */])) {

            abort(403);
        }

        // return redirect()->route('telescope');
        return $next($request);
    }
}
