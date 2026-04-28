<script setup>
import { computed, watchEffect } from 'vue';
import { Check, Minus } from 'lucide-vue-next';
import {
    MIN_PASSWORD_LENGTH,
    getPasswordEntropy,
    getPasswordMeterClass,
} from '../utils/passwordValidation';

const props = defineProps({
    password: {
        type: String,
        default: '',
    },
    email: {
        type: String,
        default: '',
    },
    firstName: {
        type: String,
        default: '',
    },
    lastName: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['validation-change']);

const FRONTEND_MIN_ZXCVBN_SCORE = 3;

const isMinLengthMet = computed(() => (props.password ?? '').length >= MIN_PASSWORD_LENGTH);
const entropy = computed(() => getPasswordEntropy(props.password, [
    props.email,
    props.firstName,
    props.lastName,
]));
const score = computed(() => entropy.value?.score ?? 0);
const strengthLabel = computed(() => {
    switch (score.value) {
        case 0:
            return 'Very Weak';
        case 1:
            return 'Weak';
        case 2:
            return 'Medium';
        case 3:
            return 'Strong';
        default:
            return 'Very Strong';
    }
});
const meterWidth = computed(() => `${score.value * 25}%`);
const meterClass = computed(() => getPasswordMeterClass(score.value));
const isEntropyMet = computed(() => entropy.value !== null && entropy.value.score >= FRONTEND_MIN_ZXCVBN_SCORE);
const validationMessage = computed(() => {
    if (!props.password) {
        return 'Password is required.';
    }

    if (!isMinLengthMet.value) {
        return `Must be at least ${MIN_PASSWORD_LENGTH} characters.`;
    }

    if (!isEntropyMet.value) {
        return 'Strength must be Medium, Strong, or Very Strong.';
    }

    return '';
});

watchEffect(() => {
    emit('validation-change', {
        message: validationMessage.value,
        score: score.value,
        isEntropyMet: isEntropyMet.value,
    });
});
</script>

<template>
    <div
        v-if="password"
        class="mt-3 space-y-2 rounded-xl border border-outline-variant/70 bg-surface-container-lowest p-3"
    >
        <div class="flex items-center justify-between text-xs">
            <span class="text-on-surface-variant">Entropy (zxcvbn)</span>
            <span class="font-semibold text-on-surface">
                {{ strengthLabel }}
            </span>
        </div>
        <div class="h-2 w-full overflow-hidden rounded-full bg-outline-variant/40">
            <div
                class="h-full rounded-full transition-all"
                :class="meterClass"
                :style="{ width: meterWidth }"
            />
        </div>
        <ul class="grid gap-1 text-xs">
            <li
                class="flex items-center gap-2"
                :class="isMinLengthMet ? 'text-green-700' : 'text-on-surface-variant'"
            >
                <span class="inline-flex w-5 items-center justify-center">
                    <Check v-if="isMinLengthMet" class="h-3.5 w-3.5" />
                    <Minus v-else class="h-3.5 w-3.5" />
                </span>
                <span>Must be at least {{ MIN_PASSWORD_LENGTH }} characters</span>
            </li>
            <li
                class="flex items-center gap-2"
                :class="isEntropyMet ? 'text-green-700' : 'text-on-surface-variant'"
            >
                <span class="inline-flex w-5 items-center justify-center">
                    <Check v-if="isEntropyMet" class="h-3.5 w-3.5" />
                    <Minus v-else class="h-3.5 w-3.5" />
                </span>
                <span>At least Strong score</span>
            </li>
        </ul>
    </div>
</template>
