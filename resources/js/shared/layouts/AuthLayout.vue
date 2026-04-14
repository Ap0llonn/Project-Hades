<script setup>
import { Link } from '@inertiajs/vue3';
import { ShieldHalf } from 'lucide-vue-next';

const year = new Date().getFullYear();
</script>

<template>
    <div class="flex min-h-screen flex-col bg-white text-gray-900">
        <header class="relative z-30">
            <nav class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-8">
                <Link href="/" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600">
                        <ShieldHalf class="h-5 w-5 text-white" />
                    </span>
                    <span class="text-xl font-bold tracking-tight text-gray-900">VaultGuardian</span>
                </Link>
            </nav>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="mx-auto w-full max-w-7xl border-t border-gray-200 px-6 py-8">
            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600">
                        <ShieldHalf class="h-4 w-4 text-white" />
                    </div>
                    <span class="text-sm font-semibold text-gray-700">VaultGuardian</span>
                </div>
                <p class="text-sm text-gray-500">&copy; {{ year }} VaultGuardian. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
