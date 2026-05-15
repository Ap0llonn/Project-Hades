<?php

namespace App\Features\Dashboard\Service\Create;

use App\Features\Dashboard\Service\Shared\ServiceType;
use Illuminate\Foundation\Http\FormRequest;

final class CreateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:login,credit_card,note,identity,card'],
            'favorite' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
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

    protected function prepareForValidation(): void
    {
        $type = ServiceType::normalize((string) $this->input('type', ''));
        if ($type !== null) {
            $this->merge(['type' => $type]);
        }
    }
}
