<?php

namespace App\Features\Dashboard\Settings\ChangePassword;

use Ecotone\Modelling\CommandBus;
use Illuminate\Http\JsonResponse;

final class ChangePasswordController
{
    public function __invoke(ChangePasswordRequest $request, CommandBus $commandBus): JsonResponse
    {
        $commandBus->send(new ChangePasswordCommand(
            userId: (string) $request->user()->id,
            currentPassword: (string) $request->input('current_password'),
            newPassword: (string) $request->input('password'),
            wrappedDek: is_array($request->input('wrapped_dek')) ? $request->input('wrapped_dek') : [],
        ));

        return response()->json(['message' => 'Password updated successfully.']);
    }
}
