<?php

namespace App\Features\Auth\MFA\MfaValidation;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MfaValidationController
{
    public function __invoke(Request $request) : Response
    {
        $redirectTo = $request->query('redirect');
        if (is_string($redirectTo) && str_starts_with($redirectTo, '/')) {
            $request->session()->put('url.intended', $redirectTo);
        }

        return Inertia::render('auth/mfa/pages/MfaValidationPage');
    }
}
