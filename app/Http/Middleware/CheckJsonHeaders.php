<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckJsonHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->header('Accept') !== 'application/json' ||
            $request->header('Content-Type') !== 'application/json'
        ) {
            return response()->json(
                [
                    'success' => false,
                    'code' => 4000,
                    'message' => 'Request denied',
                    'error' => 'Invalid request pattern.',
                ],
                200
            );
        }
        if (
            $request->header('Kosa-Target-Key') !==
            env('KOSA_TARGET_KEY') ||
            $request->header('Kosa-Source-Key') !== env('KOSA_SOURCE_KEY')
        ) {
            return response()->json(
                [
                    'success' => false,
                    'code' => 4000,
                    'message' => 'Request denied',
                    'error' => 'Unidentified Source or Target.',
                ],
                200
            );
        }
        return $next($request);
    }
}