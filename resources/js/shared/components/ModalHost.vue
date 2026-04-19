<script setup lang="ts">
import { computed } from 'vue';
import { AlertTriangle, CheckCircle2, FileWarning, X } from 'lucide-vue-next';
import { useModalStore } from '../modal/modalStore';
import type { ModalType } from '../modal/types';

const {
    activeModal,
    dismiss,
    updateFieldValue,
    submit,
    cancel,
} = useModalStore();

const getIcon = (type: ModalType) => {
    if (type === 'danger') {
        return AlertTriangle;
    }
    if (type === 'form') {
        return FileWarning;
    }

    return CheckCircle2;
};

const getIconClasses = (type: ModalType): string => {
    if (type === 'danger') {
        return 'text-white';
    }

    if (type === 'form') {
        return 'text-white';
    }

    return 'text-on-primary';
};

const getIconContainerClasses = (type: ModalType): string => {
    if (type === 'danger') {
        return 'bg-red-600 text-white dark:bg-red-500';
    }

    if (type === 'form') {
        return 'bg-amber-600 text-white dark:bg-amber-500';
    }

    return 'bg-primary text-on-primary';
};

const getConfirmButtonClasses = (type: ModalType): string => {
    if (type === 'danger') {
        return 'bg-red-600 text-white hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600';
    }

    return 'bg-slate-900 text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100';
};

const onTextInput = (event: Event, modalId: string, fieldName: string): void => {
    const target = event.target as HTMLInputElement | null;
    if (!target) {
        return;
    }

    updateFieldValue(modalId, fieldName, target.value);
};

const onTextareaInput = (event: Event, modalId: string, fieldName: string): void => {
    const target = event.target as HTMLTextAreaElement | null;
    if (!target) {
        return;
    }

    updateFieldValue(modalId, fieldName, target.value);
};

const onBackdropClick = (): void => {
    const modal = activeModal.value;
    if (!modal || modal.disableBackdropClose) {
        return;
    }

    dismiss(modal.id);
};

const isVisible = computed(() => activeModal.value !== null);
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            leave-active-class="transition duration-150 ease-in"
            enter-from-class="translate-y-2 scale-95 opacity-0"
            enter-to-class="translate-y-0 scale-100 opacity-100"
            leave-from-class="translate-y-0 scale-100 opacity-100"
            leave-to-class="translate-y-2 scale-95 opacity-0"
        >
            <div
                v-if="isVisible && activeModal"
                class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
                @click.self="onBackdropClick"
            >
                <div
                    class="relative w-full max-w-md rounded-2xl border border-outline-variant bg-surface shadow-2xl shadow-slate-950/30"
                    role="dialog"
                    aria-modal="true"
                >
                    <button
                        v-if="activeModal.cancelLabel !== null"
                        type="button"
                        class="absolute right-4 top-4 rounded-lg p-1.5 text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface"
                        :disabled="activeModal.isSubmitting"
                        @click="cancel(activeModal.id)"
                    >
                        <X class="h-4 w-4" />
                    </button>

                    <header class="px-6 pb-3 pt-6">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl" :class="getIconContainerClasses(activeModal.type)">
                                <component
                                    :is="getIcon(activeModal.type)"
                                    class="h-6 w-6"
                                    :class="getIconClasses(activeModal.type)"
                                />
                            </div>
                            <div class="pt-0.5 pr-8">
                                <h2 class="text-xl font-semibold tracking-tight text-on-surface">{{ activeModal.title }}</h2>
                                <p v-if="activeModal.message" class="mt-1 text-sm text-on-surface-variant">
                                    {{ activeModal.message }}
                                </p>
                            </div>
                        </div>
                    </header>

                    <div v-if="activeModal.type === 'form' && activeModal.form" class="space-y-4 px-6 pb-3 pt-1">
                        <label
                            v-for="field in activeModal.form.fields"
                            :key="`${activeModal.id}-${field.name}`"
                            class="block"
                        >
                            <span class="mb-1 block text-sm font-medium text-on-surface-variant">
                                {{ field.label }}
                            </span>

                            <textarea
                                v-if="field.type === 'textarea'"
                                class="w-full rounded-xl border border-outline-variant bg-surface px-3.5 py-2.5 text-sm text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                :rows="field.rows ?? 4"
                                :placeholder="field.placeholder ?? ''"
                                :value="activeModal.form.values[field.name] ?? ''"
                                @input="onTextareaInput($event, activeModal.id, field.name)"
                            />

                            <input
                                v-else
                                class="w-full rounded-xl border border-outline-variant bg-surface px-3.5 py-2.5 text-sm text-on-surface placeholder:text-on-surface-variant focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                :type="field.type ?? 'text'"
                                :placeholder="field.placeholder ?? ''"
                                :autocomplete="field.autocomplete ?? 'off'"
                                :value="activeModal.form.values[field.name] ?? ''"
                                @input="onTextInput($event, activeModal.id, field.name)"
                            >
                        </label>

                        <p v-if="activeModal.submitError" class="text-sm font-medium text-red-600">
                            {{ activeModal.submitError }}
                        </p>
                    </div>

                    <footer class="px-6 pb-6 pt-2">
                        <div class="flex items-center justify-end gap-3">
                        <button
                            v-if="activeModal.cancelLabel !== null"
                            type="button"
                            class="h-11 flex-1 cursor-pointer rounded-lg border border-outline-variant px-4 text-sm font-semibold text-on-surface transition-colors hover:bg-surface-container disabled:cursor-not-allowed disabled:opacity-70"
                            :disabled="activeModal.isSubmitting"
                            @click="cancel(activeModal.id)"
                        >
                            {{ activeModal.cancelLabel }}
                        </button>
                        <button
                            type="button"
                            class="h-11 rounded-lg cursor-pointer px-4 text-sm font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-70"
                            :class="[activeModal.cancelLabel !== null ? 'flex-1' : 'w-full', getConfirmButtonClasses(activeModal.type)]"
                            :disabled="activeModal.isSubmitting"
                            @click="submit(activeModal.id)"
                        >
                            {{ activeModal.isSubmitting ? 'Submitting...' : activeModal.confirmLabel }}
                        </button>
                        </div>
                    </footer>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
