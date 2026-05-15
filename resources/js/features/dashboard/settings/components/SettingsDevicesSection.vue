<script setup>
import SettingsSecuritySessionsSection from './security/SettingsSecuritySessionsSection.vue';

const props = defineProps({
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
    <SettingsSecuritySessionsSection
        title="Active Devices"
        description="Review active web and extension sessions and revoke access on other devices."
        :sessions="props.sessions"
        :is-loading="props.sessionsLoading"
        :revoking-session-id="props.revokingSessionId"
        :is-revoking-others="props.isRevokingOtherSessions"
        :status-message="props.sessionsStatusMessage"
        :error-message="props.sessionsErrorMessage"
        :show-top-border="false"
        @refresh-sessions="emit('refresh-sessions')"
        @revoke-session="emit('revoke-session', $event)"
        @revoke-other-sessions="emit('revoke-other-sessions')"
    />
</template>
