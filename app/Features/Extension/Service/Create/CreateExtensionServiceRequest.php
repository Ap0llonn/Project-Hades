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
            'payload' => ['required', 'array'],
            'payload.ciphertextBase64' => ['required', 'string', 'regex:/^[A-Za-z0-9+\/]+={0,2}$/'],
            'payload.ivBase64' => ['required', 'string', 'regex:/^[A-Za-z0-9+\/]+={0,2}$/'],
            'payload.version' => ['required', 'integer', 'min:1'],
            'payload.algorithm' => ['required', 'string', 'in:libsodium.crypto_secretbox'],
            'payload.encoding' => ['required', 'string', 'in:json'],
            'payload.schema' => ['required', 'integer', 'in:1'],
            'payload.createdAt' => ['required', 'date'],
        ];
    }
}
