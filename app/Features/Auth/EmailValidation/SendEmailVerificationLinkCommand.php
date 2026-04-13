<?php

namespace App\Features\Auth\EmailValidation;

final readonly class SendEmailVerificationLinkCommand
{
    public function __construct(
        public string $email,
    ) {
    }
}
