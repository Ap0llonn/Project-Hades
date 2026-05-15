<?php

namespace App\Features\ExtensionAuth\Authorize;

use Illuminate\Http\RedirectResponse;

final class BeginExtensionAuthController
{
    public function __invoke(AuthorizeExtensionAuthRequest $request): RedirectResponse
    {
        if ($request->user() !== null) {
            return redirect()->route('extension.auth.authorize', [
                'state' => $request->string('state')->toString(),
                'redirect_uri' => $request->string('redirect_uri')->toString(),
            ]);
        }

        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()->route('login');
    }
}
