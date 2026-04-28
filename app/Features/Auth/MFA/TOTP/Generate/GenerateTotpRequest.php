<?php

namespace App\Features\Auth\MFA\TOTP\Generate;

use Illuminate\Foundation\Http\FormRequest;

final class GenerateTotpRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        return true;
    }
}
