<?php

namespace App\Features\Auth\MFA\RecoveryCodes\Store;

use Illuminate\Foundation\Http\FormRequest;

class RecoveryCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'recoveryCodes' => ['required', 'array'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
