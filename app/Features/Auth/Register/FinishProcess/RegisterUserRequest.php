<?php

namespace App\Features\Auth\Register\FinishProcess;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id' => 'string|uuid',
            'password' => [
                'required',
                'string',
                Password::min(12)->uncompromised(),
            ],
            'confirm_password' => ['required', 'same:password'],
            'wrapped_private_key' => ['required', 'array'],
            'wrapped_private_key.ciphertext' => ['required', 'string'],
            'wrapped_private_key.iv' => ['required', 'string'],
            'wrapped_dek' => ['required', 'array'],
            'wrapped_dek.ciphertext' => ['required', 'string'],
            'wrapped_dek.iv' => ['required', 'string'],
            'wrapped_dek.salt' => ['required', 'string'],
            'wrapped_dek.keyLengthBits' => ['required', 'integer'],
            'wrapped_dek.kdf' => ['required', 'array'],
            'wrapped_dek.kdf.algorithm' => ['required', 'string'],
            'wrapped_dek.kdf.opsLimit' => ['required', 'integer'],
            'wrapped_dek.kdf.memoryKb' => ['required', 'integer'],
            'wrapped_dek.kdf.type' => ['required', 'string'],
            'public_key' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
