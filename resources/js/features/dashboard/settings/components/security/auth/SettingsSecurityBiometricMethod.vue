<script setup>
import {ref} from 'vue';
import {Fingerprint} from 'lucide-vue-next';

const biometricEnabled = ref(true);
</script>

<template>
    <div class="py-5">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                    <Fingerprint class="h-6 w-6"/>
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
</template>

