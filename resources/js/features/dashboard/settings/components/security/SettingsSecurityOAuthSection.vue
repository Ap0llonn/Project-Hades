<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Link as LinkIcon } from 'lucide-vue-next';
import { useModal } from '../../../../../shared/modal/index.ts';

const modal = useModal();

const settingProps = defineProps({
    security: {
        type: Object,
        default: () => ({
            oauth_providers: [],
        }),
    },
});

const oauthProviders = computed(() => {
    if (!Array.isArray(settingProps.security?.oauth_providers)) {
        return [];
    }

    return settingProps.security.oauth_providers;
});

const handleLink = (providerKey) => {
    window.location.assign(route('settings.security.oauth.link', { provider: providerKey }));
};

const handleUnlink = (providerKey) => {
    modal.danger({
        title: 'Unlink OAuth provider',
        message: 'You can still sign in with your master password and passkeys after unlinking.',
        confirmLabel: 'Unlink',
        cancelLabel: 'Cancel',
        onConfirm: async () => {
            await new Promise((resolve, reject) => {
                router.delete(route('settings.security.oauth.unlink', { provider: providerKey }), {
                    preserveScroll: true,
                    preserveState: false,
                    onSuccess: () => resolve(),
                    onError: (errors) => {
                        reject(new Error(errors.provider ?? 'Unable to unlink OAuth provider.'));
                    },
                    onCancel: () => {
                        reject(new Error('OAuth unlink was cancelled.'));
                    },
                });
            });
        },
    });
};
</script>

<template>
    <section class="border-t border-outline-variant pt-6">
        <p class="font-medium text-on-surface">OAuth Account Linking</p>
        <p class="mt-1 text-sm text-on-surface-variant">Google and Apple can be linked for sign-in. OAuth cannot create new accounts.</p>
        <p class="mt-1 text-sm text-on-surface-variant">
            Linking an OAuth provider requires immediate passkey setup to complete account security.
        </p>

        <div class="mt-4 divide-y divide-outline-variant">
            <div
                v-for="provider in oauthProviders"
                :key="provider.key"
                class="flex items-center justify-between py-3"
            >
                <div class="min-w-0">
                    <p class="text-sm font-medium text-on-surface">{{ provider.name }}</p>
                    <p class="truncate text-xs text-on-surface-variant">
                        {{ provider.linked ? `Linked as ${provider.account ?? 'Unknown account'}` : 'Not linked' }}
                    </p>
                </div>

                <button
                    v-if="provider.linked"
                    type="button"
                    class="rounded-md border border-outline-variant px-3 py-1.5 text-xs font-semibold text-on-surface-variant transition-colors hover:bg-surface-container"
                    @click="handleUnlink(provider.key)"
                >
                    Unlink
                </button>

                <button
                    v-else
                    type="button"
                    class="inline-flex items-center gap-1 rounded-md bg-primary px-3 py-1.5 text-xs font-semibold text-on-primary transition-colors hover:bg-primary-container"
                    @click="handleLink(provider.key)"
                >
                    <LinkIcon class="h-3.5 w-3.5" />
                    Link
                </button>
            </div>
        </div>
    </section>
</template>
