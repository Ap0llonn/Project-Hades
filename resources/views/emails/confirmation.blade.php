<x-mail::message>
# Confirm Your Email

Hi {{ $firstName }},

Thanks for creating your OneVault account. Confirm your email address to activate your account.

<p align="center">
    <img src="{{ $confirmationImageUrl }}" alt="Email confirmation illustration" width="420">
</p>

<x-mail::button :url="$confirmationUrl">
Confirm My Account
</x-mail::button>

If you did not create this account, you can ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
