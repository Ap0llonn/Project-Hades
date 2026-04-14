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

                <div class="hidden items-center gap-8 md:flex">
                    <a href="#features" class="text-sm text-gray-600 transition-colors hover:text-blue-600">Features</a>
                    <a href="#security" class="text-sm text-gray-600 transition-colors hover:text-blue-600">Security</a>
                    <a href="#pricing" class="text-sm text-gray-600 transition-colors hover:text-blue-600">Pricing</a>
                    <Link href="/login" class="px-5 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:text-blue-600">
                        Log In
                    </Link>
                    <Link href="/start-account" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition-all hover:bg-blue-700">
                        Sign Up
                    </Link>
                </div>
            </nav>

            <div class="mx-auto -mt-2 mb-4 flex w-full max-w-7xl items-center justify-end gap-3 px-6 md:hidden">
                <Link href="/login" class="px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:text-blue-600">
                    Log In
                </Link>
                <Link href="/start-account" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-all hover:bg-blue-700">
                    Sign Up
                </Link>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="mx-auto w-full max-w-7xl border-t border-gray-200 px-6 py-12">
            <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600">
                        <ShieldHalf class="h-4 w-4 text-white" />
                    </div>
                    <span class="text-lg font-bold tracking-tight text-gray-900">VaultGuardian</span>
                </div>

                <div class="flex gap-8 text-sm text-gray-600">
                    <a href="#" class="transition-colors hover:text-blue-600">Privacy</a>
                    <a href="#" class="transition-colors hover:text-blue-600">Terms</a>
                    <a href="#" class="transition-colors hover:text-blue-600">Security</a>
                    <a href="#" class="transition-colors hover:text-blue-600">Support</a>
                </div>

                <p class="text-sm text-gray-500">&copy; {{ year }} VaultGuardian. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
