<?php

namespace App\Features\Auth\Passkey\Create\Start;

use Illuminate\Foundation\Http\FormRequest;

class CreateStartPasskeyRequest extends FormRequest
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
