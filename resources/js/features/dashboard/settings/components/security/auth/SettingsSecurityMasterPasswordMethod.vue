<script setup>
import {KeyRound} from 'lucide-vue-next';
</script>

<template>
    <div class="flex items-center justify-between gap-4 py-5">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600">
                <KeyRound class="h-6 w-6"/>
            </div>
            <div>
                <p class="text-2xl font-semibold tracking-tight text-on-surface">Master Password</p>
                <p class="mt-1 text-sm text-on-surface-variant">Your primary authentication method</p>
            </div>
        </div>
        <button
            type="button"
            class="text-lg font-medium text-primary transition-colors hover:text-primary-container"
        >
            Change Password
        </button>
    </div>
</template>

