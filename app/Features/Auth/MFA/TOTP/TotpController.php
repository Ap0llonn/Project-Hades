<?php

namespace App\Features\Auth\MFA\TOTP;

use App\Models\MfaMethods;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use OTPHP\TOTP;

class TotpController
{
    public function setupQr(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $totp = TOTP::create();
        $secret = $totp->getSecret();

        MfaMethods::updateOrCreate(
            ['user_id' => $user->id],
            [
                'totp_enabled' => false,
                'totp_secret' => $secret,
            ]
        );

        $totp->setLabel($user->email ?? 'user@vaultguardian.test');
        $totp->setIssuer('VaultGuardian');
        $provisioningUri = $totp->getProvisioningUri();

        $renderer = new ImageRenderer(
            new RendererStyle(220),
            new SvgImageBackEnd(),
        );

        $writer = new Writer($renderer);
        $qrSvg = $writer->writeString($provisioningUri);

        return redirect()->route('settings')->with('totpSetup', [
            'setupKey' => $secret, // raw secret shown only once
            'provisioningUri' => $provisioningUri,
            'qrSvg' => $qrSvg,
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {

        $payload = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $mfaMethods = MfaMethods::query()->where('user_id', $user->id)->first();
        if (!$mfaMethods || !$mfaMethods->totp_secret) {
            throw ValidationException::withMessages([
                'code' => 'MFA is not enabled.',
            ]);
        }

        $code = preg_replace('/\D+/', '', $payload['code']);
        if ($code === null || strlen($code) !== 6) {
            throw ValidationException::withMessages([
                'code' => 'Verification code must be exactly 6 digits.',
            ]);
        }

        $secret = trim($mfaMethods->totp_secret);
        try {
            $secret = decrypt($secret);
        } catch (DecryptException) {
            // Secret is already stored as plain base32.
        }


        $totp = TOTP::create($secret);
        if (!$totp->verify($code, null, 29)) {
            throw ValidationException::withMessages([
                'code' => 'Invalid code. Check your device time and try the latest 6-digit code.',
            ]);
        }

        $mfaMethods->mfa_activated = true;
        if (Schema::hasColumn('mfa_methods', 'totp_enabled') && array_key_exists('totp_enabled', $mfaMethods->getAttributes())) {
            $mfaMethods->totp_enabled = true;
        }
        $mfaMethods->save();

        return redirect()->intended(route('dashboard'));
    }

    public function disable(Request $request): RedirectResponse
    {
        $user = $request->user();


    }
}
