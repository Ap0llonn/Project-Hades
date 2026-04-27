<?php

namespace App\Features\Auth\MFA\TOTP\VerifyChallenge;

use Illuminate\Foundation\Http\FormRequest;

final class VerifyTotpChallengeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'method' => ['required', 'string', 'in:totp,email,recovery'],
            'code' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
