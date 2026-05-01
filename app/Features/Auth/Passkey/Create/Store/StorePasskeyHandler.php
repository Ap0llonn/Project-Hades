<?php

namespace App\Features\Auth\Passkey\Create\Store;

use App\Models\User;
use Ecotone\Modelling\Attribute\CommandHandler;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Throwable;

class StorePasskeyHandler
{
    public function __construct(
        private readonly StorePasskeyAction $storePasskeyAction,
    ) {
    }

    #[CommandHandler]
    public function handle(StorePasskeyCommand $command): void
    {
        $user = User::query()->find($command->userId);

        if (!$user || !($user instanceof HasPasskeys)) {
            throw ValidationException::withMessages([
                'passkey' => 'Unable to register passkey. Please sign in again.',
            ]);
        }

        $passkeyName = trim((string) ($command->name ?? ''));
        if ($passkeyName === '') {
            $passkeyName = sprintf('%s passkey', $user->getPassKeyDisplayName());
        }

        config()->set('passkeys.relying_party.id', $command->hostName);
        config()->set('app.url', $command->appUrl);

        try {
            $this->storePasskeyAction->execute(
                $user,
                $command->passkeyJson,
                $command->passkeyOptionsJson,
                $command->hostName,
                ['name' => Str::limit($passkeyName, 120, '')],
            );
        } catch (Throwable $exception) {
            report($exception);

            $message = __('passkeys::passkeys.error_something_went_wrong_generating_the_passkey');
            if ((bool) config('app.debug')) {
                $message = sprintf('%s (%s)', $message, $exception->getMessage());
            }

            throw ValidationException::withMessages([
                'passkey' => $message,
            ]);
        }
    }
}
