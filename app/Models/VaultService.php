<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaultService extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'services';

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'favorite',
        'status',
        'data',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'string',
            'favorite' => 'boolean',
            'data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

