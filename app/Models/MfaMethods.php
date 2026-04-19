<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MfaMethods extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'email',
        'totp',
        'recovery_codes',
        'recovery_codes_show',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'string',
            'email' => 'boolean',
            'totp' => 'boolean',
            'recovery_codes' => 'array',
            'recovery_codes_show' => 'boolean',
        ];
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
