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
            'payload.ciphertextBase64' => ['required', 'string'],
            'payload.ivBase64' => ['required', 'string'],
            'payload.version' => ['sometimes', 'integer'],
            'payload.algorithm' => ['sometimes', 'string'],
            'payload.encoding' => ['sometimes', 'string'],
            'payload.schema' => ['sometimes', 'integer'],
            'payload.createdAt' => ['sometimes', 'string'],
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
