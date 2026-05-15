<?php

namespace App\Features\Dashboard\Settings\GetWrappedDek;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class GetWrappedDekController
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $vault = DB::table('vault')->where('user_id', $user->id)->first();
        if ($vault === null) {
            return response()->json(['message' => 'Vault not found.'], 404);
        }

        $wrapper = DB::table('key_wrappers')
            ->where('vault_id', $vault->id)
            ->where('type', 'password')
            ->whereNull('revoked_at')
            ->first();

        if ($wrapper === null) {
            return response()->json(['message' => 'Key wrapper not found.'], 404);
        }

        $prfParams = is_string($wrapper->prf_params)
            ? json_decode($wrapper->prf_params, true)
            : (array) $wrapper->prf_params;

        return response()->json([
            'data' => [
                'ciphertext'    => $wrapper->ciphertext,
                'iv'            => $wrapper->nonce,
                'salt'          => $wrapper->prf_salt,
                'keyLengthBits' => $prfParams['keyLengthBits'] ?? null,
                'kdf'           => [
                    'algorithm' => $prfParams['algorithm'] ?? null,
                    'opsLimit'  => $prfParams['opsLimit'] ?? null,
                    'memoryKb'  => $prfParams['memoryKb'] ?? null,
                    'type'      => $prfParams['type'] ?? null,
                ],
            ],
        ]);
    }
}
