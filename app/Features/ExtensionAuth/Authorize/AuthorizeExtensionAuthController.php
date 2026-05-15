<?php

namespace App\Features\ExtensionAuth\Authorize;

use App\Features\ExtensionAuth\Shared\ExtensionTokenService;
use Illuminate\Http\RedirectResponse;

final class AuthorizeExtensionAuthController
{
    public function __invoke(
        AuthorizeExtensionAuthRequest $request,
        ExtensionTokenService $tokenService,
    ): RedirectResponse {
        $user = $request->user();
        if ($user === null) {
            return redirect()->route('login');
        }

        $validated = $request->validated();
        $state = (string) ($validated['state'] ?? '');
        $redirectUri = (string) ($validated['redirect_uri'] ?? '');
        $issuedCode = $tokenService->issueAuthorizationCode($user, $request);
        $code = (string) ($issuedCode['code'] ?? '');

        $separator = str_contains($redirectUri, '?') ? '&' : '?';
        $targetUrl = sprintf(
            '%s%scode=%s&state=%s',
            $redirectUri,
            $separator,
            rawurlencode($code),
            rawurlencode($state),
        );

        return redirect()->away($targetUrl);
    }
}

