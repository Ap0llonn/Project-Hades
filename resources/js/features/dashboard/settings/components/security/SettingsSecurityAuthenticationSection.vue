<script setup>
import SettingsSecurityBiometricMethod from './auth/SettingsSecurityBiometricMethod.vue';
import SettingsSecurityMasterPasswordMethod from './auth/SettingsSecurityMasterPasswordMethod.vue';
import SettingsSecurityPasskeyMethod from './auth/SettingsSecurityPasskeyMethod.vue';
import SettingsSecurityTwoFactorMethod from './auth/SettingsSecurityTwoFactorMethod.vue';

const settingProps = defineProps({
    security: {
        type: Object,
        default: () => ({
            mfa_activated: false,
            totp_enabled: false,
            email_enabled: false,
            passkeys: [],
        }),
    },
    forcePasskeyPrompt: {
        type: Object,
        default: null,
    },
});
</script>

<template>
    <section>
        <div>
            <h3 class="text-2xl font-semibold tracking-tight text-on-surface">Authentication Methods</h3>
            <p class="mt-1 text-sm text-on-surface-variant">Manage how you access your vault</p>
        </div>

        <div class="mt-4 divide-y divide-outline-variant border-y border-outline-variant">
            <SettingsSecurityMasterPasswordMethod />
            <SettingsSecurityTwoFactorMethod :security="settingProps.security" />
            <SettingsSecurityPasskeyMethod
                :security="settingProps.security"
                :force-passkey-prompt="settingProps.forcePasskeyPrompt"
            />
            <SettingsSecurityBiometricMethod />
        </div>
    </section>
</template>
