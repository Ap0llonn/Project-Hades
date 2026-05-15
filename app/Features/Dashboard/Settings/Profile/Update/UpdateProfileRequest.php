<?php

namespace App\Features\Dashboard\Settings\Profile\Update;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $firstName = $this->input('first_name');
        $lastName = $this->input('last_name');

        $this->merge([
            'first_name' => is_string($firstName) ? trim($firstName) : null,
            'last_name' => is_string($lastName) ? trim($lastName) : null,
        ]);
    }
}
