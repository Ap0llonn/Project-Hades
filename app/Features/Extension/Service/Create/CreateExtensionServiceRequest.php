<?php

namespace App\Features\Extension\Service\Create;

use Illuminate\Foundation\Http\FormRequest;

final class CreateExtensionServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:2048'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:4096'],
        ];
    }
}

