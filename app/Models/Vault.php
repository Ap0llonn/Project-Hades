<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vault extends Model
{
    protected $table = 'vault';

    protected $fillable = [
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function keyWrappers(): HasMany
    {
        return $this->hasMany(KeyWrapper::class, 'vault_id', 'id');
    }
}
