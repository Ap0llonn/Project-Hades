<?php

namespace App\Features\ExtensionAuth;

use Illuminate\Foundation\Http\FormRequest;

final class ExchangeTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'min:40', 'max:255'],
        ];
    }
}

