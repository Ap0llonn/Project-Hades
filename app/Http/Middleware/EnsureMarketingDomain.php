<?php

namespace App\Http\Middleware;

use App\Support\VaultDomains;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMarketingDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getHost() !== VaultDomains::marketingHost()) {
            abort(404);
        }

        return $next($request);
    }
}
