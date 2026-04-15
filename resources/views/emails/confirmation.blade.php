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

# Confirm Your Email

Hi,

Thanks for starting your VaultGuardian account. Confirm your email address to continue your account setup.

<x-mail::button :url="$confirmationUrl" color="blue">
Verify My Email
</x-mail::button>

After verification, you will finish setup by creating your master password.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
