<?php

namespace App\Features\Auth\Register;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

final class RegisterUserController
{
    public function __invoke(RegisterUserRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $signupRequest = $request->validated();
        $commandBus->send(new RegisterUserCommand($signupRequest["email"], $signupRequest["firstName"], $signupRequest["lastName"], $signupRequest["encrypted_master_key"],
            $signupRequest["kdf_salt"], $signupRequest["kdf_params"]));

        return redirect()->route('home');
    }
}
