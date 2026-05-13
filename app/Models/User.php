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
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;

class User extends Authenticatable implements HasPasskeys
{
    use HasUuids;
    use InteractsWithPasskeys;

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

    public function vault(): HasOne
    {
        return $this->hasOne(Vault::class, 'user_id', 'id');
    }

    public function oauthAccounts(): HasMany
    {
        return $this->hasMany(OAuthAccount::class, 'user_id', 'id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(VaultService::class, 'user_id', 'id');
    }

    protected static function booted() : void
    {
        static::created(function ($user) {
            $user->mfa()->create([
                'user_id' => $user->id,
                'totp_enabled' => false,
                'totp_secret' => null,
                'email_enabled' => false,
                'recovery_codes' => [],
                'recovery_codes_show' => false,
                'mfa_activated' => false,
            ]);
        });
    }

    public function getPassKeyName(): string
    {
        return $this->email;
    }

    public function getPassKeyId(): string
    {
        return (string) $this->id;
    }

    public function getPassKeyDisplayName(): string
    {
        $fullName = trim(sprintf('%s %s', $this->first_name ?? '', $this->last_name ?? ''));

        return $fullName !== '' ? $fullName : $this->email;
    }
}
