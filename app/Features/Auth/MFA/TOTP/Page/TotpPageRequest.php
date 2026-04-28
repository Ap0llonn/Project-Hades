<?php

namespace App\Features\Auth\MFA\TOTP\Page;

use Illuminate\Foundation\Http\FormRequest;

final class TotpPageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'redirect' => ['nullable', 'string', 'starts_with:/'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
