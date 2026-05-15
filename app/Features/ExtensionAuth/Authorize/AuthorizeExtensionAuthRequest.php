<?php

namespace App\Features\ExtensionAuth\Authorize;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class AuthorizeExtensionAuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'state' => ['required', 'string', 'min:16', 'max:255'],
            'redirect_uri' => ['required', 'string', 'max:2048'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $redirectUri = (string) $this->input('redirect_uri', '');

            if (!$this->isAllowedRedirectUri($redirectUri)) {
                $validator->errors()->add('redirect_uri', 'The redirect_uri is not allowed.');
            }
        });
    }

    private function isAllowedRedirectUri(string $redirectUri): bool
    {
        if ($redirectUri === '') {
            return false;
        }

        $parts = parse_url($redirectUri);
        if (!is_array($parts)) {
            return false;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($scheme !== 'https') {
            return false;
        }

        return (bool) preg_match('/^[a-z0-9]{20,}\.chromiumapp\.org$/', $host);
    }
}

