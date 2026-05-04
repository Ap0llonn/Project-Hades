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
        }),
    },
});
</script>

<template>
    <div class="space-y-8">
        <SettingsSecurityAuthenticationSection :security="settingProps.security" />
        <SettingsSecurityOAuthSection />
        <SettingsSecuritySessionsSection />
    </div>
</template>

