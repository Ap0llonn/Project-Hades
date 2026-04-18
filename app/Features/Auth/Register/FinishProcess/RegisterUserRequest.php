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
            'encrypted_private_key' => ['required', 'array'],
            'encrypted_private_key.ciphertext' => ['required', 'string'],
            'encrypted_private_key.iv' => ['required', 'string'],
            'public_key' => ['required', 'string'],
            'kdf_salt' => ['required', 'string'],
            'kdf_params' => ['required', 'array'],
            'kdf_params.algorithm' => ['required', 'string'],
            'kdf_params.opsLimit' => ['required', 'integer'],
            'kdf_params.memoryKb' => ['required', 'integer'],
            'kdf_params.type' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
