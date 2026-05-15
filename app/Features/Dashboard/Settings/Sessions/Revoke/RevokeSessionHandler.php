<?php

namespace App\Features\Dashboard\Settings\Sessions\Revoke;

use App\Models\ExtensionAuthToken;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class RevokeSessionHandler
{
    #[CommandHandler]
    public function handle(RevokeSessionCommand $command): RevokeSessionResult
    {
        if ($command->channel === 'web') {
            if ($command->sessionId === $command->currentSessionId) {
                return RevokeSessionResult::currentSession();
            }

            if (!Schema::hasTable('sessions')) {
                return RevokeSessionResult::notFound();
            }

            $deleted = DB::table('sessions')
                ->where('id', $command->sessionId)
                ->where('user_id', $command->userId)
                ->delete();

            return $deleted > 0
                ? RevokeSessionResult::revoked()
                : RevokeSessionResult::notFound();
        }

        if ($command->channel === 'extension') {
            if (!Schema::hasTable('extension_auth_tokens')) {
                return RevokeSessionResult::notFound();
            }

            $updated = ExtensionAuthToken::query()
                ->where('id', $command->sessionId)
                ->where('user_id', $command->userId)
                ->whereNull('revoked_at')
                ->update([
                    'revoked_at' => now(),
                    'updated_at' => now(),
                ]);

            return $updated > 0
                ? RevokeSessionResult::revoked()
                : RevokeSessionResult::notFound();
        }

        return RevokeSessionResult::invalidChannel();
    }
}
