<?php

namespace App\Features\Auth\EmailValidation;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationController
{
    public function confirmation(Request $request): Response|RedirectResponse
    {
        if (! $request->session()->pull('email_confirmation_sent', false)) {
            return redirect()->route('signup');
        }

        return Inertia::render('auth/pages/EmailConfirmationPage');
    }

    public function verify(Request $request): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired link');
        }

        $user = User::findOrFail($request->id);

        if (! hash_equals(sha1($user->email), $request->hash)) {
            abort(403, 'Invalid verification link');
        }

        if (! $user->email_verified) {
            $user->email_verified = true;
            $user->save();
        }

        $request->session()->put('email_verification_success', true);

        return redirect()->route('verification.success');
    }

    public function success(Request $request): Response|RedirectResponse
    {
        if (! $request->session()->pull('email_verification_success', false)) {
            return redirect()->back();
        }

        return Inertia::render('auth/pages/EmailVerifiedPage');
    }
}
