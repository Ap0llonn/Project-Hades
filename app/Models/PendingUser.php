<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'email',
        'expires_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }
}
