<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeyWrapper extends Model
{
    protected $table = 'key_wrappers';

    protected $fillable = [
        'vault_id',
        'type',
        'ciphertext',
        'nonce',
        'tag',
        'prf_salt',
        'prf_params',
        'credential_id',
        'metadata',
        'revoked_at',
    ];

    protected function casts(): array
    {
        return [
            'vault_id' => 'integer',
            'prf_params' => 'array',
            'metadata' => 'array',
            'revoked_at' => 'datetime',
        ];
    }

    public function vault(): BelongsTo
    {
        return $this->belongsTo(Vault::class, 'vault_id', 'id');
    }
}
