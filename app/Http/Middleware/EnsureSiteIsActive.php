<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSiteIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.active', true)) {
            return response()->view('coming-soon', [], 503);
        }

        return $next($request);
    }
}

