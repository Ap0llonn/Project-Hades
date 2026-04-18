<script setup>
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import {
    Bell,
    ChevronRight,
    CreditCard,
    Database,
    Laptop,
    Shield,
    User,
} from 'lucide-vue-next';
import DashboardLayout from '../layouts/DashboardLayout.vue';

const selectedCategory = ref('all');
const activeSection = ref('profile');
const mfaMethod = ref('email');

const settingsSections = [
    {
        id: 'profile',
        label: 'Profile',
        description: 'Account identity and personal preferences',
        icon: User,
    },
    {
        id: 'security',
        label: 'Security',
        description: 'MFA, login protection, and recovery controls',
        icon: Shield,
    },
    {
        id: 'notifications',
        label: 'Notifications',
        description: 'Security alerts and email preferences',
        icon: Bell,
    },
    {
        id: 'devices',
        label: 'Devices',
        description: 'Manage trusted sessions and active devices',
        icon: Laptop,
    },
    {
        id: 'vault',
        label: 'Vault Preferences',
        description: 'Auto-lock, default options, and privacy',
        icon: Database,
    },
    {
        id: 'billing',
        label: 'Billing',
        description: 'Plan, invoices, and payment methods',
        icon: CreditCard,
    },
];

const oauthProviders = [
    {
        name: 'Google',
        linked: true,
        account: 'sam.doe@gmail.com',
    },
    {
        name: 'GitHub',
        linked: false,
        account: '',
    },
    {
        name: 'Microsoft',
        linked: false,
        account: '',
    },
];

const passkeys = [
    {
        name: 'MacBook Pro',
        createdAt: 'April 8, 2026',
    },
    {
        name: 'iPhone 16',
        createdAt: 'April 12, 2026',
    },
];

const activeSessions = [
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
];

const activeSectionConfig = computed(() =>
    settingsSections.find((section) => section.id === activeSection.value) ?? settingsSections[0],
);
</script>

<template>
    <Head title="Settings | VaultGuardian" />

    <DashboardLayout
        :selected-category="selectedCategory"
        :total-count="0"
        :favorite-count="0"
        :login-count="0"
        :card-count="0"
        :note-count="0"
        :security-alert-count="0"
        @update:selected-category="selectedCategory = $event"
    >
        <header class="sticky top-0 z-10 border-b border-outline-variant bg-surface">
            <div class="px-6 py-5 md:px-8">
                <h1 class="text-3xl font-semibold tracking-tight text-on-surface">Settings</h1>
                <p class="text-on-surface-variant">Manage account, security, and vault behavior</p>
            </div>
        </header>

        <div class="p-6 md:p-8">
            <div class="grid gap-6 lg:grid-cols-[300px_minmax(0,1fr)]">
                <aside class="rounded-2xl border border-outline-variant bg-surface p-3">
                    <p class="px-3 pb-3 pt-2 text-xs font-semibold uppercase tracking-wider text-on-surface-variant">
                        Settings Navigation
                    </p>
                    <nav class="space-y-1">
                        <button
                            v-for="section in settingsSections"
                            :key="section.id"
                            type="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-left transition-colors"
                            :class="activeSection === section.id
                                ? 'bg-secondary-container text-primary'
                                : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface'"
                            @click="activeSection = section.id"
                        >
                            <component :is="section.icon" class="h-5 w-5 shrink-0" />
                            <div class="min-w-0 flex-1">
                                <p class="font-medium">{{ section.label }}</p>
                                <p class="truncate text-xs opacity-80">{{ section.description }}</p>
                            </div>
                            <ChevronRight class="h-4 w-4 shrink-0" />
                        </button>
                    </nav>
                </aside>

                <section class="overflow-hidden rounded-2xl border border-outline-variant bg-surface">
                    <div class="border-b border-outline-variant p-6">
                        <h2 class="text-xl font-semibold tracking-tight text-on-surface">{{ activeSectionConfig.label }}</h2>
                        <p class="mt-1 text-sm text-on-surface-variant">{{ activeSectionConfig.description }}</p>
                    </div>

                    <div class="p-6">
                        <div v-if="activeSection === 'profile'" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-on-surface-variant">Full Name</span>
                                <input type="text" value="Sam Doe" class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none">
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-on-surface-variant">Email</span>
                                <input type="email" value="sam@vaultguardian.test" class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none">
                            </label>
                            <label class="block md:col-span-2">
                                <span class="mb-2 block text-sm font-medium text-on-surface-variant">Timezone</span>
                                <select class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none">
                                    <option>America/Toronto</option>
                                    <option>America/New_York</option>
                                    <option>Europe/Paris</option>
                                </select>
                            </label>
                        </div>

                        <div v-else-if="activeSection === 'security'" class="space-y-4">
                            <article class="rounded-xl border border-outline-variant bg-surface-container-low p-4">
                                <p class="font-medium text-on-surface">MFA (Email / Token)</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Choose your second factor for account sign-in protection.</p>
                                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <label class="flex items-center gap-3 rounded-lg border border-outline-variant bg-surface px-3 py-2.5">
                                        <input v-model="mfaMethod" type="radio" value="email" name="mfa_method" class="h-4 w-4 accent-primary">
                                        <span class="text-sm text-on-surface">Email code</span>
                                    </label>
                                    <label class="flex items-center gap-3 rounded-lg border border-outline-variant bg-surface px-3 py-2.5">
                                        <input v-model="mfaMethod" type="radio" value="token" name="mfa_method" class="h-4 w-4 accent-primary">
                                        <span class="text-sm text-on-surface">Authenticator token (TOTP)</span>
                                    </label>
                                </div>
                            </article>

                            <article class="rounded-xl border border-outline-variant bg-surface-container-low p-4">
                                <p class="font-medium text-on-surface">Change Password</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Update your master password and keep your vault secure.</p>
                                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                                    <input type="password" placeholder="Current password" class="rounded-lg border border-outline-variant bg-surface px-3 py-2.5 text-sm text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none">
                                    <input type="password" placeholder="New password" class="rounded-lg border border-outline-variant bg-surface px-3 py-2.5 text-sm text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none">
                                    <input type="password" placeholder="Confirm new password" class="rounded-lg border border-outline-variant bg-surface px-3 py-2.5 text-sm text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none">
                                </div>
                                <button type="button" class="mt-4 rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface">
                                    Update Password
                                </button>
                            </article>

                            <article class="rounded-xl border border-outline-variant bg-surface-container-low p-4">
                                <p class="font-medium text-on-surface">OAuth2 Account Linking</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Link external identity providers for trusted sign-in.</p>
                                <div class="mt-4 space-y-2">
                                    <div
                                        v-for="provider in oauthProviders"
                                        :key="provider.name"
                                        class="flex items-center justify-between rounded-lg border border-outline-variant bg-surface px-3 py-2.5"
                                    >
                                        <div>
                                            <p class="text-sm font-medium text-on-surface">{{ provider.name }}</p>
                                            <p class="text-xs text-on-surface-variant">
                                                {{ provider.linked ? `Linked as ${provider.account}` : 'Not linked' }}
                                            </p>
                                        </div>
                                        <button
                                            type="button"
                                            class="rounded-md px-3 py-1.5 text-xs font-semibold transition-colors"
                                            :class="provider.linked
                                                ? 'border border-outline-variant text-on-surface-variant hover:bg-surface-container'
                                                : 'bg-primary text-on-primary hover:bg-primary-container'"
                                        >
                                            {{ provider.linked ? 'Unlink' : 'Link' }}
                                        </button>
                                    </div>
                                </div>
                            </article>

                            <article class="rounded-xl border border-outline-variant bg-surface-container-low p-4">
                                <p class="font-medium text-on-surface">Passkey</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Use passkeys for phishing-resistant passwordless login.</p>
                                <div class="mt-4 space-y-2">
                                    <div
                                        v-for="passkey in passkeys"
                                        :key="passkey.name"
                                        class="rounded-lg border border-outline-variant bg-surface px-3 py-2.5"
                                    >
                                        <p class="text-sm font-medium text-on-surface">{{ passkey.name }}</p>
                                        <p class="text-xs text-on-surface-variant">Registered on {{ passkey.createdAt }}</p>
                                    </div>
                                </div>
                                <button type="button" class="mt-4 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition-colors hover:bg-primary-container">
                                    Register New Passkey
                                </button>
                            </article>

                            <article class="rounded-xl border border-outline-variant bg-surface-container-low p-4">
                                <p class="font-medium text-on-surface">Sessions</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Review and revoke active account sessions across devices.</p>
                                <div class="mt-4 space-y-2">
                                    <div
                                        v-for="session in activeSessions"
                                        :key="`${session.device}-${session.lastSeen}`"
                                        class="flex items-center justify-between rounded-lg border border-outline-variant bg-surface px-3 py-2.5"
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
                                <button type="button" class="mt-4 rounded-lg border border-outline-variant px-4 py-2 text-sm font-semibold text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface">
                                    Revoke Other Sessions
                                </button>
                            </article>
                        </div>

                        <div v-else-if="activeSection === 'notifications'" class="space-y-3">
                            <label class="flex items-center gap-3 rounded-lg border border-outline-variant bg-surface-container-low p-3">
                                <input type="checkbox" checked class="h-4 w-4 accent-primary">
                                <span class="text-sm text-on-surface">Breach alerts</span>
                            </label>
                            <label class="flex items-center gap-3 rounded-lg border border-outline-variant bg-surface-container-low p-3">
                                <input type="checkbox" checked class="h-4 w-4 accent-primary">
                                <span class="text-sm text-on-surface">New device sign-ins</span>
                            </label>
                            <label class="flex items-center gap-3 rounded-lg border border-outline-variant bg-surface-container-low p-3">
                                <input type="checkbox" class="h-4 w-4 accent-primary">
                                <span class="text-sm text-on-surface">Product updates</span>
                            </label>
                        </div>

                        <div v-else-if="activeSection === 'devices'" class="space-y-3">
                            <article class="rounded-lg border border-outline-variant bg-surface-container-low p-4">
                                <p class="font-medium text-on-surface">Current Device</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Windows 11 | Chrome | Active now</p>
                            </article>
                            <article class="rounded-lg border border-outline-variant bg-surface-container-low p-4">
                                <p class="font-medium text-on-surface">iPhone 16</p>
                                <p class="mt-1 text-sm text-on-surface-variant">iOS | Last active 2 hours ago</p>
                            </article>
                        </div>

                        <div v-else-if="activeSection === 'vault'" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-on-surface-variant">Auto-lock timeout</span>
                                <select class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none">
                                    <option>5 minutes</option>
                                    <option selected>15 minutes</option>
                                    <option>30 minutes</option>
                                </select>
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-on-surface-variant">Clipboard clear timeout</span>
                                <select class="w-full rounded-lg border border-outline-variant bg-surface px-4 py-2.5 text-on-surface focus:border-primary focus:outline-none">
                                    <option>30 seconds</option>
                                    <option selected>60 seconds</option>
                                    <option>120 seconds</option>
                                </select>
                            </label>
                        </div>

                        <div v-else class="space-y-4">
                            <article class="rounded-xl border border-outline-variant bg-surface-container-low p-4">
                                <p class="font-medium text-on-surface">Current Plan</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Pro | Renews on June 18, 2026</p>
                            </article>
                            <article class="rounded-xl border border-outline-variant bg-surface-container-low p-4">
                                <p class="font-medium text-on-surface">Payment Method</p>
                                <p class="mt-1 text-sm text-on-surface-variant">Visa ending in 4532</p>
                            </article>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button
                                type="button"
                                class="rounded-lg bg-primary px-5 py-2.5 font-semibold text-on-primary transition-colors hover:bg-primary-container"
                            >
                                Save Changes
                            </button>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </DashboardLayout>
</template>
