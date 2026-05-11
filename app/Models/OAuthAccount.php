<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OAuthAccount extends Model
{
    protected $table = 'oauth_accounts';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_user_id',
        'provider_email',
        'provider_name',
        'provider_avatar',
        'token',
        'refresh_token',
        'token_expires_at',
        'metadata',
        'linked_at',
        'unlinked_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'linked_at' => 'datetime',
            'unlinked_at' => 'datetime',
            'token_expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

