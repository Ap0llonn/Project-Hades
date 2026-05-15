<?php

namespace App\Features\Dashboard\Service\Share\LookupRecipient;

use App\Features\Dashboard\Service\Share\Shared\RecipientPublicKeyDirectory;
use Illuminate\Http\JsonResponse;

final class LookupRecipientKeyController
{
    public function __invoke(LookupRecipientKeyRequest $request, RecipientPublicKeyDirectory $directory): JsonResponse
    {
        $user = $request->user();
        if ($user === null) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $recipient = $directory->findByEmail((string) $request->validated('email'));

        if ($recipient === null) {
            return response()->json([
                'message' => 'Recipient not found.',
            ], 404);
        }

        return response()->json([
            'data' => [
                'user_id' => $recipient->userId,
                'email' => $recipient->email,
                'public_key' => $recipient->publicKey,
            ],
        ]);
    }
}
