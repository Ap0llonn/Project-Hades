<?php

namespace App\Features\Auth\Login\Identify;

use Illuminate\Foundation\Http\FormRequest;

class IdentifyRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:12'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
