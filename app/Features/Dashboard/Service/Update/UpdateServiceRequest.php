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
            'payload.ciphertextBase64' => ['required_with:payload', 'string'],
            'payload.ivBase64' => ['required_with:payload', 'string'],
            'payload.version' => ['sometimes', 'integer'],
            'payload.algorithm' => ['sometimes', 'string'],
            'payload.encoding' => ['sometimes', 'string'],
            'payload.schema' => ['sometimes', 'integer'],
            'payload.createdAt' => ['sometimes', 'string'],
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
