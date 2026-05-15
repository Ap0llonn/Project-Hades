<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVaultDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getHost() !== 'vault.vaultguardian.ca') {
            abort(404);
        }

        return $next($request);
    }
}
