<?php

namespace App\Features\Dashboard\Settings\Sessions\Revoke;

use Illuminate\Foundation\Http\FormRequest;

final class RevokeSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'channel' => ['required', 'string', 'in:web,extension'],
        ];
    }
}
