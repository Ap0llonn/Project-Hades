<?php

namespace App\Features\Dashboard\Settings\ChangePassword;

final readonly class ChangePasswordCommand
{
    public function __construct(
        public string $userId,
        public string $currentPassword,
        public string $newPassword,
        public array $wrappedDek,
    ) {}
}
