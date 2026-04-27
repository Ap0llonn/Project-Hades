<?php

namespace App\Features\Auth\MFA\Email\Challenge;

use Illuminate\Foundation\Http\FormRequest;

final class RequestEmailChallengeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'force' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

