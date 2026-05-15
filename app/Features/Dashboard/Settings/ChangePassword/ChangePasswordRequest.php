<?php

namespace App\Features\Dashboard\Settings\ChangePassword;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password'      => ['required', 'string'],
            'password'              => ['required', 'string', Password::min(12)->uncompromised()],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ];
    }
}
