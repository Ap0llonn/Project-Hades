<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMarketingDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getHost() !== 'vaultguardian.ca') {
            abort(404);
        }

        return $next($request);
    }
}
