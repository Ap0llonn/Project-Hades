<?php

namespace App\Features\ExtensionAuth\DirectLogin;

use Illuminate\Foundation\Http\FormRequest;

final class DirectLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:12'],
        ];
    }
}
