<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtensionAuthToken extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'extension_auth_tokens';

    protected $fillable = [
        'user_id',
        'access_token_hash',
        'refresh_token_hash',
        'access_expires_at',
        'refresh_expires_at',
        'last_used_at',
        'last_refreshed_at',
        'revoked_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'access_expires_at' => 'datetime',
            'refresh_expires_at' => 'datetime',
            'last_used_at' => 'datetime',
            'last_refreshed_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

