<?php

namespace App\Features\Auth\Passkey\Create\Store;

use Illuminate\Foundation\Http\FormRequest;

class StorePasskeyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'options' => ['required', 'json'],
            'passkey' => ['required', 'json'],
            'name' => ['nullable', 'string', 'max:120'],
            'wrapped_dek' => ['required', 'array'],
            'wrapped_dek.ciphertext' => ['required', 'string'],
            'wrapped_dek.iv' => ['required', 'string'],
            'wrapped_dek.prf_salt' => ['required', 'string'],
            'wrapped_dek.prf_output_length' => ['required', 'integer', 'min:16'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
