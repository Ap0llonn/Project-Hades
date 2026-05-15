<script setup>
import { computed } from 'vue';
import { Laptop, LogOut, Smartphone } from 'lucide-vue-next';

const props = defineProps({
    title: {
        type: String,
        default: 'Sessions',
    },
    description: {
        type: String,
        default: 'Review and revoke active account sessions across devices.',
    },
    sessions: {
        type: Array,
        default: () => [],
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    revokingSessionId: {
        type: String,
        default: '',
    },
    isRevokingOthers: {
        type: Boolean,
        default: false,
    },
    statusMessage: {
        type: String,
        default: '',
    },
    errorMessage: {
        type: String,
        default: '',
    },
    showTopBorder: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits([
    'refresh-sessions',
    'revoke-session',
    'revoke-other-sessions',
]);

const hasRevokableSessions = computed(() => props.sessions.some((session) => session.can_revoke === true));

const formatLastActive = (rawDate) => {
    if (!rawDate) {
        return 'Unknown activity';
    }

    const date = new Date(rawDate);
    if (Number.isNaN(date.getTime())) {
        return 'Unknown activity';
    }

    const now = Date.now();
    const diffSeconds = Math.round((date.getTime() - now) / 1000);
    const absDiff = Math.abs(diffSeconds);

    if (absDiff < 60) {
        return 'Active now';
    }

    if (absDiff < 3600) {
        const minutes = Math.round(diffSeconds / 60);
        return new Intl.RelativeTimeFormat('en', { numeric: 'auto' }).format(minutes, 'minute');
    }

    if (absDiff < 86400) {
        const hours = Math.round(diffSeconds / 3600);
        return new Intl.RelativeTimeFormat('en', { numeric: 'auto' }).format(hours, 'hour');
    }

    const days = Math.round(diffSeconds / 86400);
    return new Intl.RelativeTimeFormat('en', { numeric: 'auto' }).format(days, 'day');
};

const getSessionDetails = (session) => {
    const location = session.ip_address ? `IP ${session.ip_address}` : 'IP unavailable';
    return `${location} | ${formatLastActive(session.last_active_at)}`;
};

const getSessionIcon = (session) => {
    const platform = String(session.platform ?? '').toLowerCase();
    if (platform.includes('iphone') || platform.includes('ipad') || platform.includes('android')) {
        return Smartphone;
    }

    return Laptop;
};
</script>

<template>
    <section :class="showTopBorder ? 'border-t border-outline-variant pt-6' : ''">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="font-medium text-on-surface">{{ title }}</p>
                <p class="mt-1 text-sm text-on-surface-variant">{{ description }}</p>
            </div>
            <button
                type="button"
                class="rounded-md border border-outline-variant px-3 py-1.5 text-xs font-semibold text-on-surface-variant transition-colors hover:bg-surface-container"
                :disabled="isLoading"
                @click="emit('refresh-sessions')"
            >
                Refresh
            </button>
        </div>

        <p v-if="statusMessage" class="mt-3 text-sm text-primary">{{ statusMessage }}</p>
        <p v-if="errorMessage" class="mt-3 text-sm text-red-600">{{ errorMessage }}</p>

        <div class="mt-4 divide-y divide-outline-variant">
            <div
                v-for="session in sessions"
                :key="`${session.channel}-${session.id}`"
                class="flex items-center justify-between gap-4 py-3"
            >
                <div class="min-w-0">
                    <p class="inline-flex items-center gap-2 text-sm font-medium text-on-surface">
                        <component :is="getSessionIcon(session)" class="h-4 w-4 shrink-0" />
                        <span class="truncate">{{ session.device_name || 'Unknown device' }}</span>
                    </p>
                    <p class="truncate text-xs text-on-surface-variant">{{ getSessionDetails(session) }}</p>
                    <p class="mt-1 text-xs text-on-surface-variant">
                        {{ session.channel === 'extension' ? 'Extension session' : 'Web session' }}
                    </p>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <span
                        class="rounded-full px-2 py-1 text-xs font-medium"
                        :class="session.is_current ? 'bg-secondary-container text-primary' : 'bg-surface-container text-on-surface-variant'"
                    >
                        {{ session.is_current ? 'Current' : 'Active' }}
                    </span>

                    <button
                        v-if="session.can_revoke"
                        type="button"
                        class="inline-flex items-center gap-1 rounded-md border border-outline-variant px-3 py-1.5 text-xs font-semibold text-on-surface-variant transition-colors hover:bg-surface-container"
                        :disabled="revokingSessionId === session.id || isRevokingOthers"
                        @click="emit('revoke-session', session)"
                    >
                        <LogOut class="h-3.5 w-3.5" />
                        {{ revokingSessionId === session.id ? 'Revoking...' : 'Revoke' }}
                    </button>
                </div>
            </div>

            <p
                v-if="!isLoading && sessions.length === 0"
                class="py-3 text-sm text-on-surface-variant"
            >
                No active sessions.
            </p>
        </div>

        <button
            type="button"
            class="mt-4 rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="!hasRevokableSessions || isRevokingOthers || isLoading"
            @click="emit('revoke-other-sessions')"
        >
            {{ isRevokingOthers ? 'Revoking...' : 'Revoke Other Sessions' }}
        </button>
    </section>
</template>
