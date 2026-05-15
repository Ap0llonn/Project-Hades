<?php

namespace App\Features\Dashboard\Service\Share\Shared;

interface RecipientPublicKeyDirectory
{
    public function findByEmail(string $email): ?RecipientPublicKey;
}
