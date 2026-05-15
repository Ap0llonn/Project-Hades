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

# New Shared Vault Item

Hi,

**{{ $ownerEmail }}** shared a vault item with you:

## {{ $serviceName }}

Open your dashboard to view and decrypt it.

<x-mail::button :url="$dashboardUrl" color="blue">
Open Dashboard
</x-mail::button>

If this was unexpected, review your account security settings.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
