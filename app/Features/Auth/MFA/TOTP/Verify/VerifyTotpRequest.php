<?php

namespace App\Features\Auth\MFA\TOTP\Verify;

use Illuminate\Foundation\Http\FormRequest;

final class VerifyTotpRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
