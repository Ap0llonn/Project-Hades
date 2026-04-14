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

        $pendingUser = PendingUser::find($request->id);

        if (! $pendingUser || now()->gt($pendingUser->expires_at)) {
            return redirect()->route('start-account');
        }

        if (User::query()->where('email', $pendingUser->email)->exists()) {
            return redirect()->route('login');
        }

        $commandBus->send(new RegisterUserCommand(
            $pendingUser->email,
            $request->input('encrypted_master_key'),
            $request->input('kdf_salt'),
            $request->input('kdf_params'),
        ));

        $pendingUser->delete();

        return redirect()->route('login');
    }
}
