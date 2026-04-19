<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'email',
        'email_hashed',
        'password_hash',
        'first_name',
        'last_name',
        'private_key_wrapper',
        'kdf_salt',
        'kdf_params',
        'email_verified',
        'public_key',
    ];

    protected $casts = [
        'kdf_params' => 'array',
        'private_key_wrapper' => 'array',
    ];

    public function mfa(): HasOne
    {
        return $this->hasOne(MfaMethods::class, 'user_id', 'id');
    }

    protected static function booted() : void
    {
        static::created(function ($user) {
            $user->mfa()->create([
                'user_id' => $user->id,
                'email' => true,
                'totp' => false,
                'recovery_codes' => [],
            ]);
        });
    }
}
