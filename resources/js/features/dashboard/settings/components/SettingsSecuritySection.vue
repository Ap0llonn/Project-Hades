<script setup>
import SettingsSecurityAuthenticationSection from './security/SettingsSecurityAuthenticationSection.vue';
import SettingsSecurityOAuthSection from './security/SettingsSecurityOAuthSection.vue';
import SettingsSecuritySessionsSection from './security/SettingsSecuritySessionsSection.vue';

const settingProps = defineProps({
    security: {
        type: Object,
        default: () => ({
            mfa_activated: false,
            totp_enabled: false,
            email_enabled: false,
            passkeys: [],
            oauth_providers: [],
            oauth_passkey_prompt: null,
        }),
    },
});
</script>

<template>
    <div class="space-y-8">
        <SettingsSecurityAuthenticationSection :security="settingProps.security" />
        <SettingsSecurityOAuthSection :security="settingProps.security" />
        <SettingsSecuritySessionsSection />
    </div>
</template>
