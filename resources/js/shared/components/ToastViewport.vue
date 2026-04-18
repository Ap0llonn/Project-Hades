<script setup lang="ts">
import { computed } from 'vue';
import { AlertTriangle, CheckCircle2, FileWarning, X } from 'lucide-vue-next';
import { useToastStore } from '../toast/toastStore';
import type { ToastType } from '../toast/types';

const {
    toasts,
    removeToast,
    updateFormFieldValue,
    submitFormToast,
    cancelFormToast,
} = useToastStore();

const getIcon = (type: ToastType) => {
    if (type === 'danger') {
        return AlertTriangle;
    }
    if (type === 'form') {
        return FileWarning;
    }

    return CheckCircle2;
};

const getToastClasses = (type: ToastType) => {
    if (type === 'danger') {
        return 'border-red-300 bg-error-container/20 text-on-surface';
    }
    if (type === 'form') {
        return 'border-outline-variant bg-secondary-container/35 text-on-surface';
    }

    return 'border-outline-variant bg-surface text-on-surface';
};

const getIconClasses = (type: ToastType) => {
    if (type === 'danger') {
        return 'text-red-600';
    }
    if (type === 'form') {
        return 'text-primary';
    }

    return 'text-primary';
};

const onTextInput = (event: Event, toastId: string, fieldName: string): void => {
    const target = event.target as HTMLInputElement | null;
    if (!target) {
        return;
    }

    updateFormFieldValue(toastId, fieldName, target.value);
};

const onTextareaInput = (event: Event, toastId: string, fieldName: string): void => {
    const target = event.target as HTMLTextAreaElement | null;
    if (!target) {
        return;
    }

    updateFormFieldValue(toastId, fieldName, target.value);
};

const hasToasts = computed(() => toasts.value.length > 0);
</script>

<template>
    <Teleport to="body">
        <div v-if="hasToasts" class="pointer-events-none fixed right-4 top-4 z-[100] w-[min(92vw,420px)] space-y-3">
            <TransitionGroup
                name="toast-stack"
                tag="div"
                class="space-y-3"
                enter-active-class="transition-all duration-200 ease-out"
                leave-active-class="transition-all duration-150 ease-in"
                enter-from-class="translate-y-2 opacity-0"
                leave-to-class="translate-y-1 opacity-0"
                move-class="transition-transform duration-200"
            >
                <article
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="pointer-events-auto rounded-xl border p-4 shadow-lg shadow-slate-900/10"
                    :class="getToastClasses(toast.type)"
                >
                    <div class="flex items-start gap-3">
                        <component
                            :is="getIcon(toast.type)"
                            class="mt-0.5 h-5 w-5 shrink-0"
                            :class="getIconClasses(toast.type)"
                        />

                        <div class="min-w-0 flex-1">
                            <p v-if="toast.title" class="font-semibold tracking-tight">{{ toast.title }}</p>
                            <p v-if="toast.message" class="mt-1 text-sm text-on-surface-variant">{{ toast.message }}</p>

                            <div v-if="toast.type === 'form' && toast.form" class="mt-3 space-y-3">
                                <label
                                    v-for="field in toast.form.fields"
                                    :key="`${toast.id}-${field.name}`"
                                    class="block"
                                >
                                    <span class="mb-1 block text-xs font-medium text-on-surface-variant">
                                        {{ field.label }}
                                    </span>

                                    <textarea
                                        v-if="field.type === 'textarea'"
                                        class="w-full rounded-lg border border-outline-variant bg-surface px-3 py-2 text-sm text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none"
                                        :rows="field.rows ?? 3"
                                        :placeholder="field.placeholder ?? ''"
                                        :value="toast.form.values[field.name] ?? ''"
                                        @input="onTextareaInput($event, toast.id, field.name)"
                                    />

                                    <input
                                        v-else
                                        class="w-full rounded-lg border border-outline-variant bg-surface px-3 py-2 text-sm text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none"
                                        :type="field.type ?? 'text'"
                                        :placeholder="field.placeholder ?? ''"
                                        :autocomplete="field.autocomplete ?? 'off'"
                                        :value="toast.form.values[field.name] ?? ''"
                                        @input="onTextInput($event, toast.id, field.name)"
                                    >
                                </label>

                                <p v-if="toast.form.submitError" class="text-sm text-red-600">
                                    {{ toast.form.submitError }}
                                </p>

                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        class="rounded-md border border-outline-variant px-3 py-1.5 text-sm font-medium text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface"
                                        :disabled="toast.form.isSubmitting"
                                        @click="cancelFormToast(toast.id)"
                                    >
                                        {{ toast.form.cancelLabel }}
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded-md bg-primary px-3 py-1.5 text-sm font-medium text-on-primary transition-colors hover:bg-primary-container disabled:cursor-not-allowed disabled:opacity-70"
                                        :disabled="toast.form.isSubmitting"
                                        @click="submitFormToast(toast.id)"
                                    >
                                        {{ toast.form.isSubmitting ? 'Submitting...' : toast.form.submitLabel }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button
                            v-if="toast.type !== 'form'"
                            type="button"
                            class="rounded-md p-1 text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface"
                            @click="removeToast(toast.id)"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>
                </article>
            </TransitionGroup>
        </div>
    </Teleport>
</template>
