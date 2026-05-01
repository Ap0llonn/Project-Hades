<?php

namespace App\Features\Auth\Passkey\Login\Authenticate;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticateUsingPasskeyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_authentication_response' => ['required', 'json'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
