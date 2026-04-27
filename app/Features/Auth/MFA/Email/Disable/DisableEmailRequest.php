<?php

namespace App\Features\Auth\MFA\Email\Disable;

use Illuminate\Foundation\Http\FormRequest;

final class DisableEmailRequest extends FormRequest
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

