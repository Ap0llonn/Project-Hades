<?php

namespace App\Features\Dashboard\Service\Read;

use App\Features\Dashboard\Service\Shared\ServiceType;
use Illuminate\Foundation\Http\FormRequest;

final class ReadServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => ['sometimes', 'uuid'],
            'type' => ['sometimes', 'string', 'in:login,credit_card,note,identity,card'],
            'status' => ['sometimes', 'string', 'in:active,archived'],
            'search' => ['sometimes', 'string', 'max:255'],
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

