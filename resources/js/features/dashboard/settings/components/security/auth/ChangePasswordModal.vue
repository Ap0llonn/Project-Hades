<script setup lang="ts">
import { computed, ref } from 'vue';
import { X, Eye, EyeOff } from 'lucide-vue-next';
import axios from 'axios';
import PasswordEntropyVerification from '../../../../../auth/register/components/PasswordEntropyVerification.vue';
import {
    MIN_PASSWORD_LENGTH,
    MIN_ZXCVBN_SCORE,
    getPasswordEntropy,
} from '../../../../../auth/register/utils/passwordValidation';

const emit = defineEmits<{ close: [] }>();

const currentPassword = ref('');
const newPassword = ref('');
const confirmPassword = ref('');
const showCurrent = ref(false);
const showNew = ref(false);
const showConfirm = ref(false);
const submitting = ref(false);
const error = ref('');

const entropy = computed(() => getPasswordEntropy(newPassword.value));
const entropyScore = computed(() => entropy.value?.score ?? 0);
const isLengthMet = computed(() => newPassword.value.length >= MIN_PASSWORD_LENGTH);
const isEntropyMet = computed(() => entropyScore.value >= MIN_ZXCVBN_SCORE);
const isMatch = computed(() => newPassword.value !== '' && newPassword.value === confirmPassword.value);

const canSubmit = computed(
    () => currentPassword.value.trim() !== ''
        && isLengthMet.value
        && isEntropyMet.value
        && isMatch.value
        && !submitting.value,
);

async function submit() {
    if (!canSubmit.value) return;
    error.value = '';
    submitting.value = true;

    try {
        await axios.put('/settings/password', {
            current_password: currentPassword.value,
            password: newPassword.value,
            password_confirmation: confirmPassword.value,
        });
        emit('close');
    } catch (e: any) {
        const data = e?.response?.data;
        const firstError = data?.errors
            ? Object.values(data.errors as Record<string, string[]>).flat()[0]
            : (data?.message ?? 'An error occurred. Please try again.');
        error.value = firstError;
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <Teleport to="body">
        <div
            class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
            @click.self="emit('close')"
        >
            <div class="relative w-full max-w-md overflow-hidden rounded-2xl border border-outline-variant bg-surface shadow-2xl shadow-slate-950/30">
                <header class="flex items-center justify-between border-b border-outline-variant px-6 py-4">
                    <h2 class="text-lg font-semibold tracking-tight text-on-surface">Change Password</h2>
                    <button
                        type="button"
                        class="rounded-lg p-1.5 text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface"
                        @click="emit('close')"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </header>

                <div class="space-y-4 px-6 pb-3 pt-4">
                    <!-- Current password -->
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium text-on-surface-variant">Current Password</span>
                        <div class="relative">
                            <input
                                :type="showCurrent ? 'text' : 'password'"
                                v-model="currentPassword"
                                autocomplete="current-password"
                                class="w-full rounded-xl border border-outline-variant bg-surface px-3.5 py-2.5 pr-10 text-sm text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                placeholder="Enter your current password"
                            />
                            <button
                                type="button"
                                tabindex="-1"
                                class="absolute inset-y-0 right-3 flex items-center text-on-surface-variant"
                                @click="showCurrent = !showCurrent"
                            >
                                <EyeOff v-if="showCurrent" class="h-4 w-4" />
                                <Eye v-else class="h-4 w-4" />
                            </button>
                        </div>
                    </label>

                    <!-- New password -->
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium text-on-surface-variant">New Password</span>
                        <div class="relative">
                            <input
                                :type="showNew ? 'text' : 'password'"
                                v-model="newPassword"
                                autocomplete="new-password"
                                class="w-full rounded-xl border border-outline-variant bg-surface px-3.5 py-2.5 pr-10 text-sm text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                placeholder="Enter your new password"
                            />
                            <button
                                type="button"
                                tabindex="-1"
                                class="absolute inset-y-0 right-3 flex items-center text-on-surface-variant"
                                @click="showNew = !showNew"
                            >
                                <EyeOff v-if="showNew" class="h-4 w-4" />
                                <Eye v-else class="h-4 w-4" />
                            </button>
                        </div>
                        <PasswordEntropyVerification :password="newPassword" />
                    </label>

                    <!-- Confirm password -->
                    <label class="block">
                        <span class="mb-1 block text-sm font-medium text-on-surface-variant">Confirm New Password</span>
                        <div class="relative">
                            <input
                                :type="showConfirm ? 'text' : 'password'"
                                v-model="confirmPassword"
                                autocomplete="new-password"
                                class="w-full rounded-xl border border-outline-variant bg-surface px-3.5 py-2.5 pr-10 text-sm text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                placeholder="Confirm your new password"
                            />
                            <button
                                type="button"
                                tabindex="-1"
                                class="absolute inset-y-0 right-3 flex items-center text-on-surface-variant"
                                @click="showConfirm = !showConfirm"
                            >
                                <EyeOff v-if="showConfirm" class="h-4 w-4" />
                                <Eye v-else class="h-4 w-4" />
                            </button>
                        </div>
                        <p
                            v-if="confirmPassword && !isMatch"
                            class="mt-1 text-xs text-red-600"
                        >
                            Passwords do not match.
                        </p>
                    </label>

                    <p v-if="error" class="text-sm font-medium text-red-600">{{ error }}</p>
                </div>

                <footer class="px-6 pb-6 pt-2">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="h-11 flex-1 cursor-pointer rounded-lg border border-outline-variant px-4 text-sm font-semibold text-on-surface transition-colors hover:bg-surface-container disabled:cursor-not-allowed disabled:opacity-70"
                            :disabled="submitting"
                            @click="emit('close')"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="h-11 flex-1 cursor-pointer rounded-lg bg-primary px-4 text-sm font-semibold text-on-primary transition-colors hover:bg-primary-container disabled:cursor-not-allowed disabled:opacity-70"
                            :disabled="!canSubmit"
                            @click="submit"
                        >
                            {{ submitting ? 'Updating...' : 'Update Password' }}
                        </button>
                    </div>
                </footer>
            </div>
        </div>
    </Teleport>
</template>
