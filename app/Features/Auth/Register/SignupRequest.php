<?php

namespace App\Features\Auth\Register;

use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'fistName' => 'required|string',
            'lastName' => 'required|string'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
