<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceShare extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'service_shares';

    protected $fillable = [
        'service_id',
        'owner_user_id',
        'recipient_user_id',
        'key_envelope',
    ];

    protected function casts(): array
    {
        return [
            'service_id' => 'string',
            'owner_user_id' => 'string',
            'recipient_user_id' => 'string',
            'key_envelope' => 'array',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(VaultService::class, 'service_id', 'id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id', 'id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_user_id', 'id');
    }
}
