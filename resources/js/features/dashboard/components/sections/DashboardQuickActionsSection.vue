<script setup>
import { Key, Users, Zap } from 'lucide-vue-next';

const emit = defineEmits(['select-category']);
</script>

<template>
    <section class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
        <button
            class="group flex items-center gap-4 rounded-xl border border-outline-variant bg-surface p-6 text-left transition-all hover:border-primary"
            @click="emit('select-category', 'password-generator')"
        >
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container to-tertiary-fixed transition-transform group-hover:scale-110">
                <Key class="h-6 w-6 text-primary" />
            </div>
            <div>
                <p class="mb-1 font-semibold text-on-surface">Generate Password</p>
                <p class="text-sm text-on-surface-variant">Create a strong password</p>
            </div>
        </button>

        <button
            class="group flex items-center gap-4 rounded-xl border border-outline-variant bg-surface p-6 text-left transition-all hover:border-primary"
            @click="emit('select-category', 'import-export')"
        >
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container to-tertiary-fixed transition-transform group-hover:scale-110">
                <Zap class="h-6 w-6 text-primary" />
            </div>
            <div>
                <p class="mb-1 font-semibold text-on-surface">Import / Export</p>
                <p class="text-sm text-on-surface-variant">Move your vault data securely</p>
            </div>
        </button>

        <button
            class="group flex items-center gap-4 rounded-xl border border-outline-variant bg-surface p-6 text-left transition-all hover:border-primary"
            @click="emit('select-category', 'password-sharing')"
        >
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gradient-to-br from-secondary-container to-tertiary-fixed transition-transform group-hover:scale-110">
                <Users class="h-6 w-6 text-primary" />
            </div>
            <div>
                <p class="mb-1 font-semibold text-on-surface">Share Passwords</p>
                <p class="text-sm text-on-surface-variant">With family or team</p>
            </div>
        </button>
    </section>
</template>

