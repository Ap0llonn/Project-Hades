<?php

namespace App\Features\Dashboard\Settings\ChangePassword;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password'      => ['required', 'string'],
            'password'              => ['required', 'string', Password::min(12)->uncompromised()],
            'password_confirmation' => ['required', 'string', 'same:password'],
            'wrapped_dek' => ['required', 'array'],
            'wrapped_dek.ciphertext' => ['required', 'string'],
            'wrapped_dek.iv' => ['required', 'string'],
            'wrapped_dek.salt' => ['required', 'string'],
            'wrapped_dek.keyLengthBits' => ['required', 'integer', 'min:128'],
            'wrapped_dek.kdf' => ['required', 'array'],
            'wrapped_dek.kdf.algorithm' => ['required', 'string', 'in:argon2id13,argon2i13'],
            'wrapped_dek.kdf.opsLimit' => ['required', 'integer', 'min:1'],
            'wrapped_dek.kdf.memoryKb' => ['required', 'integer', 'min:1'],
            'wrapped_dek.kdf.type' => ['required', 'string', 'in:Argon2id13,Argon2i13'],
        ];
    }
}
