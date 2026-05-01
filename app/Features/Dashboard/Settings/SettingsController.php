<?php

namespace App\Features\Dashboard\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\LaravelPasskeys\Models\Passkey;

class SettingsController
{
    public function __invoke() : Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        $passkeys = [];

        if (
            $user !== null
            && Schema::hasTable('passkeys')
            && class_exists(Passkey::class)
        ) {
            $passkeys = $user
                ->passkeys()
                ->orderByDesc('last_used_at')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn (Passkey $passkey): array => [
                    'id' => $passkey->id,
                    'name' => $passkey->name,
                    'created_at' => optional($passkey->created_at)?->toIso8601String(),
                    'last_used_at' => optional($passkey->last_used_at)?->toIso8601String(),
                ])
                ->all();
        }

        return Inertia::render('dashboard/settings/pages/SettingsPage', [
            'security' => [
                'mfa_activated' =>  $user->mfa->mfa_activated ?? false,
                'totp_enabled' => $user->mfa->totp_enabled ?? false,
                'email_enabled' => $user->mfa->email_enabled ?? false,
                'passkeys' => $passkeys,
            ]
        ]);
    }
}
