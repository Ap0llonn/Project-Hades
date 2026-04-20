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
        'totp_enabled',
        'totp_secret',
        'recovery_codes',
        'recovery_codes_show',
        'mfa_activated'
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'string',
            'totp_enabled' => 'boolean',
            'totp_secret' => 'string',
            'recovery_codes' => 'array',
            'recovery_codes_show' => 'boolean',
            'mfa_activated' => 'boolean',
        ];
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public static function booted() : void
    {
        static::saving(function ($mfa) {
            $attributes = $mfa->getAttributes();

            if (array_key_exists('totp_enabled', $attributes)) {
                $mfa->mfa_activated = (bool) $mfa->totp_enabled;
                return;
            }

            if ($mfa->mfa_activated === null) {
                $mfa->mfa_activated = false;
            }
        });
    }
}
