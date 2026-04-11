<?php

namespace App\Features\Auth\Register;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;

final class SignupController
{
    public function __invoke(SignupRequest $request, CommandBus $commandBus): RedirectResponse
    {

        return redirect()->route('home');
    }
}
