<script setup>
import { Link } from '@inertiajs/vue3';
import { LockKeyhole, LogIn, ShieldCheck, UserPlus } from 'lucide-vue-next';

const navigation = [
    { label: 'Vault', href: '#', active: true },
    { label: 'Features', href: '#features', active: false },
    { label: 'Security Audit', href: '#security-audit', active: false },
    { label: 'Pricing', href: '#pricing', active: false },
];

const footerLinks = [
    { label: 'Privacy Policy', href: '#' },
    { label: 'Terms of Service', href: '#' },
    { label: 'Security Whitepaper', href: '#' },
];

const year = new Date().getFullYear();
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-on-surface selection:bg-primary/20">
        <header class="sticky top-0 z-40 border-b border-outline-variant/50 bg-surface/80 backdrop-blur-xl">
            <nav class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-3">
                <div class="flex items-center gap-8">
                    <Link
                        href="/"
                        class="inline-flex items-center gap-2 text-xl font-bold tracking-tight text-primary"
                    >
                        <LockKeyhole class="h-5 w-5" />
                        <span>The Vault</span>
                    </Link>
                    <div class="hidden items-center gap-6 md:flex">
                        <a
                            v-for="item in navigation"
                            :key="item.label"
                            :href="item.href"
                            class="border-b-2 py-1 text-sm font-semibold transition-colors duration-200"
                            :class="item.active
                                ? 'border-primary text-primary'
                                : 'border-transparent text-on-surface-variant hover:text-on-surface'"
                        >
                            {{ item.label }}
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        href="/login"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:text-primary"
                    >
                        <LogIn class="h-4 w-4" />
                        <span>Login</span>
                    </Link>
                    <Link
                        href="/signup"
                        class="inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-2 text-sm font-semibold text-on-primary shadow-md transition-all hover:bg-primary-container active:scale-95"
                    >
                        <UserPlus class="h-4 w-4" />
                        <span>Sign Up</span>
                    </Link>
                </div>
            </nav>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <footer class="mt-auto border-t border-outline-variant/50 bg-surface py-8">
            <div class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-6 px-8 md:flex-row">
                <div class="flex flex-col items-center gap-2 md:items-start">
                    <span class="inline-flex items-center gap-2 text-sm font-bold">
                        <ShieldCheck class="h-4 w-4 text-primary" />
                        <span>The Vault Security</span>
                    </span>
                    <p class="text-xs font-medium text-on-surface-variant">
                        &copy; {{ year }} The Vault Security. All rights reserved.
                    </p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-6 md:justify-end">
                    <a
                        v-for="item in footerLinks"
                        :key="item.label"
                        :href="item.href"
                        class="text-xs font-medium text-on-surface-variant transition-colors hover:text-primary"
                    >
                        {{ item.label }}
                    </a>
                </div>
            </div>
        </footer>
    </div>
</template>
