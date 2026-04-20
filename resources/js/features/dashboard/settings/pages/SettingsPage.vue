<script setup>
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Bell, ChevronRight, CreditCard, Database, Laptop, Shield, User } from 'lucide-vue-next';
import { route } from 'ziggy-js';
import DashboardLayout from '../../layouts/DashboardLayout.vue';
import SettingsBillingSection from '../components/SettingsBillingSection.vue';
import SettingsDevicesSection from '../components/SettingsDevicesSection.vue';
import SettingsNotificationsSection from '../components/SettingsNotificationsSection.vue';
import SettingsProfileSection from '../components/SettingsProfileSection.vue';
import SettingsSecuritySection from '../components/SettingsSecuritySection.vue';
import SettingsVaultSection from '../components/SettingsVaultSection.vue';
import { useModal } from '../../../../shared/modal/index.ts';

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

const openTotpSetupModal = (payload) => {
    modal.form({
        title: 'Set up authenticator app',
        message: 'Scan this QR code, then enter the 6-digit code from your authenticator app.',
        qrSvg: payload.qrSvg,
        confirmLabel: 'Activate',
        cancelLabel: 'Cancel',
        fields: [
            {
                name: 'setupKey',
                label: 'Setup key',
                required: true,
                initialValue: payload.setupKey ?? '',
            },
            {
                name: 'verificationCode',
                label: 'Verification code',
                placeholder: '123456',
                autocomplete: 'one-time-code',
                required: true,
            },
        ],
        onSubmit: async (values) => {
            const verificationCode = values.verificationCode.trim();

            if (!/^\d{6}$/.test(verificationCode)) {
                throw new Error('Verification code must be exactly 6 digits.');
            }

            await new Promise((resolve, reject) => {
                router.post(
                    route('mfa.totp.verify'),
                    {
                        code: verificationCode,
                    },
                    {
                        preserveScroll: true,
                        preserveState: true,
                        onSuccess: () => resolve(),
                        onError: (errors) => {
                            reject(new Error(errors.code ?? 'Unable to verify authenticator code.'));
                        },
                        onCancel: () => {
                            reject(new Error('TOTP verification was cancelled.'));
                        },
                    },
                );
            });

            mfaTotpEnabled.value = true;
            twoFactorEnabled.value = true;

            modal.confirmation({
                title: 'Authenticator app activated',
                message: 'TOTP is now configured for your account.',
                confirmLabel: 'Close',
                cancelLabel: null,
            });
        },
    });
};

const handleSaveChanges = () => {
    modal.confirmation({
        title: 'Settings updated',
        message: 'Your settings have been saved.',
        confirmLabel: 'Close',
        cancelLabel: null,
    });
};

const toggleTwoFactor = () => {
    twoFactorEnabled.value = !twoFactorEnabled.value;
};

const toggleMfaEmail = () => {
    mfaEmailEnabled.value = !mfaEmailEnabled.value;
};

const togglePasskey = () => {
    passkeyEnabled.value = !passkeyEnabled.value;
};

const toggleBiometric = () => {
    biometricEnabled.value = !biometricEnabled.value;
};

const handleTotpAction = () => {
    if (mfaTotpEnabled.value) {
        modal.confirmation({
            title: 'Edit authenticator app',
            message: 'TOTP edit flow is not wired yet. Keep your current setup for now.',
            confirmLabel: 'Close',
            cancelLabel: null,
        });
        return;
    }

    router.post(route('mfa.totp.setup-qr'), {}, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
            const payload = page.props?.flash?.totpSetup;
            if (!payload || !payload.qrSvg) {
                modal.danger({
                    title: 'TOTP setup unavailable',
                    message: 'Unable to generate TOTP setup QR.',
                    confirmLabel: 'Close',
                    cancelLabel: null,
                });
                return;
            }

            openTotpSetupModal(payload);
        },
        onError: (errors) => {
            modal.danger({
                title: 'TOTP setup unavailable',
                message: errors.code ?? 'Unable to generate TOTP setup QR.',
                confirmLabel: 'Close',
                cancelLabel: null,
            });
        },
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
                        <SettingsProfileSection v-if="activeSection === 'profile'" />

                        <SettingsSecuritySection
                            v-else-if="activeSection === 'security'"
                            :two-factor-enabled="twoFactorEnabled"
                            :mfa-email-enabled="mfaEmailEnabled"
                            :mfa-totp-enabled="mfaTotpEnabled"
                            :passkey-enabled="passkeyEnabled"
                            :biometric-enabled="biometricEnabled"
                            :oauth-providers="oauthProviders"
                            :passkeys="passkeys"
                            :active-sessions="activeSessions"
                            @toggle-two-factor="toggleTwoFactor"
                            @toggle-email="toggleMfaEmail"
                            @toggle-passkey="togglePasskey"
                            @toggle-biometric="toggleBiometric"
                            @totp-action="handleTotpAction"
                        />

                        <SettingsNotificationsSection v-else-if="activeSection === 'notifications'" />
                        <SettingsDevicesSection v-else-if="activeSection === 'devices'" />
                        <SettingsVaultSection v-else-if="activeSection === 'vault'" />
                        <SettingsBillingSection v-else />

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
