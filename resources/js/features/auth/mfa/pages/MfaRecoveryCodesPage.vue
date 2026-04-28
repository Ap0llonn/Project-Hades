<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import { Copy, Download, KeyRound, ShieldAlert } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import AuthLayout from '../../../../shared/layouts/AuthLayout.vue';
import { useModal } from '../../../../shared/modal';
import CryptoGenerator from '../../../../shared/utils/crypto/CryptoGenerator';

const recoveryCodes = ref([]);
const copyStatus = ref('');
const modal = useModal();

const recoveryTokensRequest = useForm({
    recoveryCodes: []
})

onMounted(async () => {
    recoveryCodes.value = await CryptoGenerator.generateRecoveryTokens();
});

const copyAllCodes = async () => {
    copyStatus.value = '';
    const payload = recoveryCodes.value.join('\n');

    try {
        await navigator.clipboard.writeText(payload);
        copyStatus.value = 'All recovery codes copied.';
    } catch {
        copyStatus.value = 'Clipboard unavailable. Copy manually.';
    }
};

const downloadCodesFile = () => {
    copyStatus.value = '';
    const payload = `${recoveryCodes.value.join('\n')}\n`;
    const file = new Blob([payload], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(file);
    const link = document.createElement('a');

    link.href = url;
    link.download = 'vaultguardian-recovery-codes.txt';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    copyStatus.value = 'Recovery codes file downloaded.';
};

const confirmRecoveryCodesSaved = () => {
    modal.danger({
        title: 'Final warning before continuing',
        message: 'If you did not store these recovery codes, you may lose account access if MFA is unavailable.',
        confirmLabel: 'I saved them safely',
        cancelLabel: 'Not yet',
        onConfirm: async () => {
            const hashedTokens = await CryptoGenerator.hashTokens(recoveryCodes.value);
            recoveryTokensRequest.recoveryCodes = hashedTokens;
            recoveryTokensRequest.post(route('mfa.recovery-codes.send'));
        },
    });
};
</script>

<template>
    <Head title="Recovery Codes | VaultGuardian" />

    <AuthLayout>
        <div class="relative min-h-full overflow-x-hidden bg-white text-gray-900">
            <div class="pointer-events-none fixed inset-0 opacity-40">
                <div
                    class="absolute inset-0"
                    style="
                        background-image:
                            linear-gradient(rgba(59, 130, 246, 0.08) 1px, transparent 1px),
                            linear-gradient(90deg, rgba(59, 130, 246, 0.08) 1px, transparent 1px);
                        background-size: 60px 60px;
                    "
                />
            </div>

            <div class="fixed left-1/4 top-0 h-96 w-96 rounded-full bg-blue-500 opacity-15 blur-[120px]" />
            <div class="fixed bottom-0 right-1/4 h-96 w-96 rounded-full bg-blue-400 opacity-10 blur-[120px]" />

            <div class="relative z-10 flex min-h-[calc(100vh-120px)] items-center justify-center px-6 py-12">
                <div class="w-full max-w-2xl">
                    <div class="rounded-2xl border border-outline-variant bg-surface p-8 shadow-xl shadow-blue-500/10">
                        <div class="mb-8 text-center">
                            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-secondary-container">
                                <KeyRound class="h-8 w-8 text-on-secondary-container" />
                            </div>
                            <h1 class="mb-2 text-3xl tracking-tight text-on-surface" style="font-weight: 700;">
                                Recovery Codes
                            </h1>
                            <p class="text-on-surface-variant">
                                Save these one-time codes in a secure place. Each code can be used once.
                            </p>
                        </div>

                        <div class="mb-6 rounded-xl border border-outline-variant bg-error-container/30 p-4">
                            <div class="flex items-start gap-3">
                                <ShieldAlert class="mt-0.5 h-5 w-5 text-on-surface" />
                                <p class="text-sm text-on-surface">
                                    Keep these recovery codes in a safe place. Anyone with them can access your account.
                                </p>
                            </div>
                        </div>

                        <div class="mb-8 rounded-xl border border-outline-variant bg-surface-container-low p-4">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div
                                    v-for="code in recoveryCodes"
                                    :key="code"
                                    class="rounded-lg border border-outline-variant bg-surface px-4 py-3 text-center font-mono text-sm tracking-widest text-on-surface"
                                >
                                    {{ code }}
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <button
                                type="button"
                                class="inline-flex items-center cursor-pointer justify-center gap-2 rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 text-on-surface transition-colors hover:bg-surface-container"
                                style="font-weight: 600;"
                                @click="copyAllCodes"
                            >
                                <Copy class="h-4 w-4" />
                                Copy all codes
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center cursor-pointer justify-center gap-2 rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 text-on-surface transition-colors hover:bg-surface-container"
                                style="font-weight: 600;"
                                @click="downloadCodesFile"
                            >
                                <Download class="h-4 w-4" />
                                Download .txt
                            </button>
                        </div>
                        <p v-if="copyStatus" class="mb-6 text-sm text-on-surface-variant">
                            {{ copyStatus }}
                        </p>

                        <button
                            type="button"
                            class="w-full rounded-xl cursor-pointer bg-primary px-6 py-4 text-on-primary transition-colors hover:opacity-90"
                            style="font-weight: 600;"
                            @click="confirmRecoveryCodesSaved"
                        >
                            I saved my recovery codes
                        </button>

                        <div>
                            <p>{{recoveryTokensRequest.errors}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>
