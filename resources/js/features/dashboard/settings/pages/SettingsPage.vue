<script setup>
import { computed, ref } from 'vue';
import {Head, usePage} from '@inertiajs/vue3';
import { Bell, ChevronRight, CreditCard, Database, Laptop, Shield, User } from 'lucide-vue-next';
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

const settingProps = defineProps({
    security: {
        type: Object,
        default: () => ({
            mfa_activated: false,
            totp_enabled: false,
            email_enabled: false,
            passkeys: [],
            oauth_providers: [],
            oauth_passkey_prompt: null,
        }),
    }
})

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
                        <SettingsProfileSection v-if="activeSection === 'profile'" />

                        <SettingsSecuritySection v-else-if="activeSection === 'security'" :security="settingProps.security" />

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
