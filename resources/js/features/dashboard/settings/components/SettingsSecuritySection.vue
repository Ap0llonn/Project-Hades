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
    forcePasskeyPrompt: {
        type: Object,
        default: null,
    },
    sessions: {
        type: Array,
        default: () => [],
    },
    sessionsLoading: {
        type: Boolean,
        default: false,
    },
    revokingSessionId: {
        type: String,
        default: '',
    },
    isRevokingOtherSessions: {
        type: Boolean,
        default: false,
    },
    sessionsStatusMessage: {
        type: String,
        default: '',
    },
    sessionsErrorMessage: {
        type: String,
        default: '',
    },
});

const emit = defineEmits([
    'refresh-sessions',
    'revoke-session',
    'revoke-other-sessions',
]);
</script>

<template>
    <div class="space-y-8">
        <SettingsSecurityAuthenticationSection
            :security="settingProps.security"
            :force-passkey-prompt="settingProps.forcePasskeyPrompt"
        />
        <SettingsSecurityOAuthSection :security="settingProps.security" />
        <SettingsSecuritySessionsSection
            :sessions="settingProps.sessions"
            :is-loading="settingProps.sessionsLoading"
            :revoking-session-id="settingProps.revokingSessionId"
            :is-revoking-others="settingProps.isRevokingOtherSessions"
            :status-message="settingProps.sessionsStatusMessage"
            :error-message="settingProps.sessionsErrorMessage"
            @refresh-sessions="emit('refresh-sessions')"
            @revoke-session="emit('revoke-session', $event)"
            @revoke-other-sessions="emit('revoke-other-sessions')"
        />
    </div>
</template>
