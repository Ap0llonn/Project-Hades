<?php

namespace App\Features\Dashboard\Settings\Profile\Update;

final readonly class UpdateProfileCommand
{
    public function __construct(
        public string $userId,
        public ?string $firstName,
        public ?string $lastName,
    ) {
    }
}
