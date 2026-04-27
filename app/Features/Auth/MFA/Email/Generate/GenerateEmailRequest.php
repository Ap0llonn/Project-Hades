<?php

namespace App\Features\Auth\MFA\Email\Generate;

use Illuminate\Foundation\Http\FormRequest;

final class GenerateEmailRequest extends FormRequest
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

