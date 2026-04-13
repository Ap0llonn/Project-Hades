<?php

namespace App\Features\Auth\Register;

use App\Features\Auth\EmailValidation\SendEmailVerificationLinkCommand;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class RegisterUserController
{
    public function __invoke(RegisterUserRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $signupRequest = $request->validated();

        $commandBus->send(new RegisterUserCommand(
            $signupRequest['email'],
            $signupRequest['firstName'],
            $signupRequest['lastName'],
            $signupRequest['encrypted_master_key'],
            $signupRequest['kdf_salt'],
            $signupRequest['kdf_params'],
        ));
        $commandBus->send(new SendEmailVerificationLinkCommand($signupRequest['email']));

        return redirect()
            ->route('email.confirmation')
            ->with('email_confirmation_sent', true);
    }
}
