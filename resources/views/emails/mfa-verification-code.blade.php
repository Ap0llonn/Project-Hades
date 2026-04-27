<x-mail::message>
<p style="text-align: center;">
    <img
        src="{{ $message->embed(public_path('images/appIcon.png')) }}"
        alt="VaultGuardian Logo"
        width="56"
        height="56"
        style="border-radius: 12px;"
    >
</p>

# Verify Your Sign-In

Use this verification code to continue:

## {{ $verificationCode }}

This code expires in **{{ $expiresInMinutes }} minutes**.

If you did not request this code, you can safely ignore this email.

Need help? Contact support at **{{ $supportEmail ?? 'support@vaultguardian.test' }}**.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
