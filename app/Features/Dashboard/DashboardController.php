<?php

namespace App\Features\Dashboard;

use App\Models\OAuthAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController
{
    public function __invoke() : Response|RedirectResponse
    {
        $user = Auth::user();
        if (
            $user !== null
            && Schema::hasTable('oauth_accounts')
            && OAuthAccount::query()
                ->where('user_id', (string) $user->id)
                ->whereNull('unlinked_at')
                ->where('metadata->requires_passkey_setup', true)
                ->exists()
        ) {
            return redirect()
                ->route('settings')
                ->with('error', 'Passkey setup is required to finish OAuth linking.');
        }

        return Inertia::render('dashboard/pages/DashboardPage');
    }
}
