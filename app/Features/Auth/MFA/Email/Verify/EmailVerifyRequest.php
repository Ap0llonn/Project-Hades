<?php

namespace App\Features\Auth\MFA\Email\Verify;

use Illuminate\Foundation\Http\FormRequest;

class EmailVerifyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'regex:/^\d{6}$/'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
