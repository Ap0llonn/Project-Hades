<script setup>
import {ref} from 'vue';

const activeSessions = ref([
    {
        device: 'Windows 11 | Chrome',
        location: 'Montreal, CA',
        lastSeen: 'Active now',
        current: true,
    },
    {
        device: 'iPhone 16 | iOS App',
        location: 'Montreal, CA',
        lastSeen: '2 hours ago',
        current: false,
    },
    {
        device: 'MacBook Pro | Safari',
        location: 'Quebec, CA',
        lastSeen: 'Yesterday',
        current: false,
    },
]);
</script>

<template>
    <section class="border-t border-outline-variant pt-6">
        <p class="font-medium text-on-surface">Sessions</p>
        <p class="mt-1 text-sm text-on-surface-variant">
            Review and revoke active account sessions across devices.
        </p>
        <div class="mt-4 divide-y divide-outline-variant">
            <div
                v-for="session in activeSessions"
                :key="`${session.device}-${session.lastSeen}`"
                class="flex items-center justify-between py-3"
            >
                <div>
                    <p class="text-sm font-medium text-on-surface">{{ session.device }}</p>
                    <p class="text-xs text-on-surface-variant">{{ session.location }} | {{ session.lastSeen }}</p>
                </div>
                <span
                    class="rounded-full px-2 py-1 text-xs font-medium"
                    :class="session.current ? 'bg-secondary-container text-primary' : 'bg-surface-container text-on-surface-variant'"
                >
                    {{ session.current ? 'Current' : 'Active' }}
                </span>
            </div>
        </div>
        <button
            type="button"
            class="mt-4 rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface"
        >
            Revoke Other Sessions
        </button>
    </section>
</template>

