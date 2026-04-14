<x-mail::message>
# Confirm Your Email

Hi,

Thanks for starting your VaultGuardian account. Confirm your email address to continue your account setup.

<x-mail::button :url="$confirmationUrl">
Verify My Email
</x-mail::button>

After verification, you will finish setup by creating your master password.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
