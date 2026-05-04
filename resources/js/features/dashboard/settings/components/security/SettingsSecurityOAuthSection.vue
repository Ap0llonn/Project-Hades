<script setup>
import {ref} from 'vue';

const oauthProviders = ref([
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
]);
</script>

<template>
    <section class="border-t border-outline-variant pt-6">
        <p class="font-medium text-on-surface">OAuth2 Account Linking</p>
        <p class="mt-1 text-sm text-on-surface-variant">Link external identity providers for trusted sign-in.</p>
        <div class="mt-4 divide-y divide-outline-variant">
            <div
                v-for="provider in oauthProviders"
                :key="provider.name"
                class="flex items-center justify-between py-3"
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
    </section>
</template>

