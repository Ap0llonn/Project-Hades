<script setup>
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import {
    Bell,
    ChevronRight,
    CreditCard,
    Database,
    Fingerprint,
    KeyRound,
    Laptop,
    Shield,
    Smartphone,
    User,
} from 'lucide-vue-next';
import DashboardLayout from '../layouts/DashboardLayout.vue';
import { useModal } from '../../../shared/modal';

const selectedCategory = ref('all');
const activeSection = ref('profile');
const twoFactorEnabled = ref(false);
const mfaEmailEnabled = ref(true);
const mfaTotpEnabled = ref(false);
const passkeyEnabled = ref(false);
const biometricEnabled = ref(true);

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
        description: 'MFA and login protection controls',
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

const modal = useModal();

const handleSaveChanges = () => {
    modal.confirmation({
        title: 'Settings updated',
        message: 'Your settings have been saved.',
        confirmLabel: 'Close',
        cancelLabel: null,
    });
};
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
                            <article class="overflow-hidden rounded-xl border border-outline-variant bg-surface-container-low">
                                <div class="border-b border-outline-variant p-5">
                                    <h3 class="text-2xl font-semibold tracking-tight text-on-surface">Authentication Methods</h3>
                                    <p class="mt-1 text-sm text-on-surface-variant">Manage how you access your vault</p>
                                </div>

                                <div class="divide-y divide-outline-variant">
                                    <div class="flex items-center justify-between gap-4 p-5">
                                        <div class="flex items-center gap-4">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600">
                                                <KeyRound class="h-6 w-6" />
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

                                    <div class="p-5">
                                        <div class="flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-600">
                                                    <Smartphone class="h-6 w-6" />
                                                </div>
                                                <div>
                                                    <p class="text-2xl font-semibold tracking-tight text-on-surface">Two-Factor Authentication (2FA)</p>
                                                    <p class="mt-1 text-sm text-on-surface-variant">Add an extra layer of security with 2FA</p>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors"
                                                :class="twoFactorEnabled ? 'bg-primary' : 'bg-surface-container-high'"
                                                @click="twoFactorEnabled = !twoFactorEnabled"
                                            >
                                                <span
                                                    class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform"
                                                    :class="twoFactorEnabled ? 'translate-x-7' : 'translate-x-1'"
                                                />
                                            </button>
                                        </div>

                                        <div v-if="twoFactorEnabled" class="mt-4 border-t border-outline-variant pt-4">
                                            <p class="mb-2 text-sm font-semibold uppercase tracking-wider text-on-surface-variant">Two-factor methods</p>

                                            <article class="flex items-start justify-between gap-4 border-b border-outline-variant py-4">
                                                <div class="flex min-w-0 items-start gap-3">
                                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-secondary-container text-primary">
                                                        <Smartphone class="h-5 w-5" />
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <p class="font-semibold text-on-surface">Authenticator app</p>
                                                            <span
                                                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                                                :class="mfaTotpEnabled
                                                                    ? 'bg-secondary-container text-primary'
                                                                    : 'bg-surface-container-high text-on-surface-variant'"
                                                            >
                                                                {{ mfaTotpEnabled ? 'Configured' : 'Not configured' }}
                                                            </span>
                                                        </div>
                                                        <p class="mt-1 text-sm text-on-surface-variant">
                                                            Use an authenticator app or browser extension to get verification codes.
                                                        </p>
                                                    </div>
                                                </div>
                                                <button
                                                    type="button"
                                                    class="shrink-0 rounded-md border border-primary px-3 py-1 text-sm font-medium text-primary transition-colors hover:bg-primary hover:text-on-primary"
                                                    @click="mfaTotpEnabled = !mfaTotpEnabled"
                                                >
                                                    {{ mfaTotpEnabled ? 'Edit' : 'Setup' }}
                                                </button>
                                            </article>

                                            <article class="flex items-start justify-between gap-4 pt-4">
                                                <div class="flex min-w-0 items-start gap-3">
                                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-surface-container text-on-surface-variant">
                                                        <Bell class="h-5 w-5" />
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-2">
                                                            <p class="font-semibold text-on-surface">Email code</p>
                                                            <span
                                                                class="rounded-full px-2 py-0.5 text-xs font-medium"
                                                                :class="mfaEmailEnabled
                                                                    ? 'bg-secondary-container text-primary'
                                                                    : 'bg-surface-container-high text-on-surface-variant'"
                                                            >
                                                                {{ mfaEmailEnabled ? 'Configured' : 'Not configured' }}
                                                            </span>
                                                        </div>
                                                        <p class="mt-1 text-sm text-on-surface-variant">
                                                            Receive one-time codes by email when additional verification is required.
                                                        </p>
                                                    </div>
                                                </div>
                                                <button
                                                    type="button"
                                                    class="shrink-0 rounded-md border border-primary px-3 py-1 text-sm font-medium text-primary transition-colors hover:bg-primary hover:text-on-primary"
                                                    @click="mfaEmailEnabled = !mfaEmailEnabled"
                                                >
                                                    {{ mfaEmailEnabled ? 'Edit' : 'Setup' }}
                                                </button>
                                            </article>
                                        </div>
                                    </div>

                                    <div class="p-5">
                                        <div class="flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                                                    <Shield class="h-6 w-6" />
                                                </div>
                                                <div>
                                                    <p class="text-2xl font-semibold tracking-tight text-on-surface">Passkey</p>
                                                    <p class="mt-1 text-sm text-on-surface-variant">Sign in without a password</p>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors"
                                                :class="passkeyEnabled ? 'bg-primary' : 'bg-surface-container-high'"
                                                @click="passkeyEnabled = !passkeyEnabled"
                                            >
                                                <span
                                                    class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform"
                                                    :class="passkeyEnabled ? 'translate-x-7' : 'translate-x-1'"
                                                />
                                            </button>
                                        </div>

                                        <div v-if="passkeyEnabled" class="mt-4 space-y-2 border-t border-outline-variant pt-4">
                                            <div
                                                v-for="passkey in passkeys"
                                                :key="passkey.name"
                                                class="rounded-lg border border-outline-variant bg-surface px-3 py-2.5"
                                            >
                                                <p class="text-sm font-medium text-on-surface">{{ passkey.name }}</p>
                                                <p class="text-xs text-on-surface-variant">Registered on {{ passkey.createdAt }}</p>
                                            </div>
                                            <button type="button" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition-colors hover:bg-primary-container">
                                                Register New Passkey
                                            </button>
                                        </div>
                                    </div>

                                    <div class="p-5">
                                        <div class="flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                                                    <Fingerprint class="h-6 w-6" />
                                                </div>
                                                <div>
                                                    <p class="text-2xl font-semibold tracking-tight text-on-surface">Biometric Security</p>
                                                    <p class="mt-1 text-sm text-on-surface-variant">Face ID, Touch ID, or Windows Hello</p>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors"
                                                :class="biometricEnabled ? 'bg-primary' : 'bg-surface-container-high'"
                                                @click="biometricEnabled = !biometricEnabled"
                                            >
                                                <span
                                                    class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform"
                                                    :class="biometricEnabled ? 'translate-x-7' : 'translate-x-1'"
                                                />
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
                                @click="handleSaveChanges"
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
