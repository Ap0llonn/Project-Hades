<?php

namespace App\Features\Auth\Login\Authenticate;

use Illuminate\Foundation\Http\FormRequest;

final class AuthenticateRequest extends FormRequest
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
