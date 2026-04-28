<?php

namespace App\Features\Auth\MFA\TOTP\Disable;

use Illuminate\Foundation\Http\FormRequest;

final class DisableTotpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'masterPassword' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
