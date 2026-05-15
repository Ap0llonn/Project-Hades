<?php

namespace App\Http\Middleware;

use App\Support\VaultDomains;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVaultDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getHost() !== VaultDomains::vaultHost()) {
            abort(404);
        }

        return $next($request);
    }
}
