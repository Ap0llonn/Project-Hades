<?php

namespace App\Features\Auth\Register;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'confirm_password' => ['required', 'same:password'],

            'firstName' => ['required', 'string'],
            'lastName' => ['required', 'string'],

            'encrypted_master_key' => ['required', 'array'],
            'encrypted_master_key.ciphertext' => ['required', 'string'],
            'encrypted_master_key.iv' => ['required', 'string'],

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
