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
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
