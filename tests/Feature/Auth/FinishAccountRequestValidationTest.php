<?php

namespace Tests\Feature\Auth;

use App\Models\PendingUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinishAccountRequestValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_finish_account_request_returns_validation_errors(): void
    {
        $this->withoutMiddleware();

        $pendingUser = PendingUser::create([
            'email' => 'new.user@example.com',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this
            ->withSession([
                'verified_signup_email' => 'new.user@example.com',
                'verified_pending_user_id' => $pendingUser->id,
            ])
            ->post(route('finish-account.perform'), []);

        $response->assertInvalid([
            'password',
            'confirm_password',
            'encrypted_master_key',
            'kdf_salt',
            'kdf_params',
        ]);
    }
}
