<?php

namespace App\Features\Auth\Register\FinishProcess;

use App\Models\PendingUser;
use App\Models\User;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

final class RegisterUserController
{
    public function __invoke(RegisterUserRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $verifiedPendingUserId = (string) $request->session()->get('finish_account.pending_user_id', '');
        if ($verifiedPendingUserId === '') {
            return redirect()->route('start-account');
        }

        $pendingUser = PendingUser::find($verifiedPendingUserId);

        if (! $pendingUser || now()->gt($pendingUser->expires_at) || $pendingUser->used_at === null) {
            $request->session()->forget('finish_account.pending_user_id');
            return redirect()->route('start-account');
        }

        if (User::query()->where('email', $pendingUser->email)->exists()) {
            return redirect()->route('login');
        }

        $commandBus->send(new RegisterUserCommand(
            $pendingUser->email,
            $request->input('password'),
            $request->input('encrypted_master_key'),
            $request->input('kdf_salt'),
            $request->input('kdf_params'),
        ));

        $pendingUser->delete();
        $request->session()->forget('finish_account.pending_user_id');

        return redirect()->route('login');
    }
}
