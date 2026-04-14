<?php

namespace App\Features\Auth\EmailValidation;

use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationController
{
    public function confirmation(Request $request): Response|RedirectResponse
    {
        if (! $request->session()->pull('email_confirmation_sent', false)) {
            return redirect()->route('start-account');
        }

        return Inertia::render('auth/pages/EmailConfirmationPage', [
            'email' => (string) $request->session()->get('email_confirmation_email', ''),
        ]);
    }

    public function verify(Request $request): Response|RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            return $this->errorResponse('Invalid signature', 'Invalid or expired link.');
        }

        $pendingUser = PendingUser::find($request->id);
        if (! $pendingUser) {
            return $this->errorResponse('Verification not found', 'The verification request could not be found.');
        }

        if (! filter_var($pendingUser->email, FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse('Invalid verification link', 'Invalid verification link.');
        }

        if (now()->gt($pendingUser->expires_at)) {
            return $this->errorResponse('Expired link', 'This verification link has expired.');
        }

        if ($pendingUser->used_at !== null) {
            return $this->errorResponse('Link already used', 'This verification link was already used.');
        }

        if (User::query()->where('email', $pendingUser->email)->exists()) {
            return redirect()->route('login');
        }

        $pendingUser->update([
            'used_at' => now(),
        ]);

        $setupUrl = URL::temporarySignedRoute(
            'finish-account',
            now()->addMinutes(10),
            ['id' => $pendingUser->id],
        );

        return redirect($setupUrl);
    }

    public function success(Request $request): RedirectResponse
    {
        if ($request->session()->pull('email_verification_success', false)) {
            return redirect()->route('finish-account');
        }

        return redirect()->route('start-account');
    }

    public function errorResponse($title, $messages): Response
    {
        return Inertia::render('shared/pages/ErrorPage', [
            'errorCode' => 403,
            'title' => $title,
            'message' => $messages,
        ]);
    }
}
