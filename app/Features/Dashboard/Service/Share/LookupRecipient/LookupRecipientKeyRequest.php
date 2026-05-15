<?php

namespace App\Features\Dashboard\Service\Share\LookupRecipient;

use Illuminate\Foundation\Http\FormRequest;

final class LookupRecipientKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
        ];
    }
}
