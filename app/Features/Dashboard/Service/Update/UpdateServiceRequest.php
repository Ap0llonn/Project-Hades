<?php

namespace App\Features\Dashboard\Service\Update;

use App\Features\Dashboard\Service\Shared\ServiceType;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'string', 'in:login,credit_card,note,identity,card'],
            'favorite' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
            'payload' => ['sometimes', 'array'],
            'payload.ciphertextBase64' => ['required_with:payload', 'string', 'regex:/^[A-Za-z0-9+\/]+={0,2}$/'],
            'payload.ivBase64' => ['required_with:payload', 'string', 'regex:/^[A-Za-z0-9+\/]+={0,2}$/'],
            'payload.version' => ['required_with:payload', 'integer', 'min:1'],
            'payload.algorithm' => ['required_with:payload', 'string', 'in:libsodium.crypto_secretbox'],
            'payload.encoding' => ['required_with:payload', 'string', 'in:json'],
            'payload.schema' => ['required_with:payload', 'integer', 'in:1'],
            'payload.createdAt' => ['required_with:payload', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $type = $this->input('type');
        if (!is_string($type)) {
            return;
        }

        $normalized = ServiceType::normalize($type);
        if ($normalized !== null) {
            $this->merge(['type' => $normalized]);
        }
    }
}
