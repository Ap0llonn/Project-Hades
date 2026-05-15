<?php

namespace App\Features\Dashboard\Service\Share\Create;

use Illuminate\Foundation\Http\FormRequest;

final class ShareServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_email' => ['required', 'string', 'email', 'max:255'],
            'key_envelope' => ['required', 'array'],
            'key_envelope.ciphertextBase64' => ['required', 'string'],
            'key_envelope.algorithm' => ['required', 'string', 'in:libsodium.crypto_box_seal,libsodium.crypto_box_easy'],
            'key_envelope.ivBase64' => ['required_if:key_envelope.algorithm,libsodium.crypto_box_easy', 'string'],
            'key_envelope.senderPublicKeyBase64' => ['sometimes', 'string'],
            'key_envelope.version' => ['sometimes', 'integer', 'min:1'],
            'key_envelope.schema' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
