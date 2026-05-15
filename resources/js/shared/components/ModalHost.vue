<script setup lang="ts">
import { computed, onBeforeUnmount, watch } from 'vue';
import { X } from 'lucide-vue-next';
import { useModalStore } from '../modal/modalStore';
import type { ModalField, ModalSize, ModalType } from '../modal/types';

const {
    activeModal,
    dismiss,
    updateFieldValue,
    submit,
    cancel,
} = useModalStore();

const getConfirmButtonClasses = (type: ModalType): string => {
    if (type === 'danger') {
        return 'bg-red-600 text-white hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600';
    }

    return 'bg-primary text-on-primary hover:bg-primary-container';
};

const getModalWidthClasses = (size: ModalSize): string => {
    if (size === 'sm') {
        return 'max-w-md';
    }
    if (size === 'lg') {
        return 'max-w-2xl';
    }

    return 'max-w-xl';
};

const getModalHeightClasses = (size: ModalSize): string => {
    if (size === 'lg') {
        return 'h-[85vh]';
    }

    return '';
};

const getFormSections = (fields: ModalField[]): Array<{ title: string; fields: ModalField[] }> => {
    const groups: Array<{ title: string; fields: ModalField[] }> = [];

    fields.forEach((field) => {
        const title = field.section ?? '';
        const existingGroup = groups.find((group) => group.title === title);
        if (existingGroup) {
            existingGroup.fields.push(field);
            return;
        }

        groups.push({
            title,
            fields: [field],
        });
    });

    return groups;
};

const getModalWidthClasses = (size: ModalSize): string => {
    if (size === 'sm') {
        return 'max-w-md';
    }
    if (size === 'lg') {
        return 'max-w-2xl';
    }

    return 'max-w-xl';
};

const getModalHeightClasses = (size: ModalSize): string => {
    if (size === 'lg') {
        return 'h-[85vh]';
    }

    return '';
};

const getFormSections = (fields: ModalField[]): Array<{ title: string; fields: ModalField[] }> => {
    const groups: Array<{ title: string; fields: ModalField[] }> = [];

    fields.forEach((field) => {
        const title = field.section ?? '';
        const existingGroup = groups.find((group) => group.title === title);
        if (existingGroup) {
            existingGroup.fields.push(field);
            return;
        }

        groups.push({
            title,
            fields: [field],
        });
    });

    return groups;
};

const onTextInput = (event: Event, modalId: string, fieldName: string): void => {
    const target = event.target as HTMLInputElement | null;
    if (!target) {
        return;
    }

    if (target.type === 'checkbox') {
        updateFieldValue(modalId, fieldName, target.checked ? 'true' : 'false');
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

let previousBodyOverflow = '';
let previousHtmlOverflow = '';
let scrollLockApplied = false;

watch(isVisible, (visible) => {
    if (typeof document === 'undefined') {
        return;
    }

    if (visible && !scrollLockApplied) {
        previousBodyOverflow = document.body.style.overflow;
        previousHtmlOverflow = document.documentElement.style.overflow;
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
        scrollLockApplied = true;
        return;
    }

    if (!visible && scrollLockApplied) {
        document.body.style.overflow = previousBodyOverflow;
        document.documentElement.style.overflow = previousHtmlOverflow;
        scrollLockApplied = false;
    }
});

onBeforeUnmount(() => {
    if (typeof document === 'undefined' || !scrollLockApplied) {
        return;
    }

    document.body.style.overflow = previousBodyOverflow;
    document.documentElement.style.overflow = previousHtmlOverflow;
    scrollLockApplied = false;
});
</script>

<template>
    <Teleport to="body">
        <div
            v-if="isVisible && activeModal"
            class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
            @click.self="onBackdropClick"
        >
            <Transition
                appear
                enter-active-class="transition duration-200 ease-out"
                leave-active-class="transition duration-150 ease-in"
                enter-from-class="translate-y-2 scale-95 opacity-0"
                enter-to-class="translate-y-0 scale-100 opacity-100"
                leave-from-class="translate-y-0 scale-100 opacity-100"
                leave-to-class="translate-y-2 scale-95 opacity-0"
            >
                <div
                    v-if="activeModal"
                    class="relative flex w-full flex-col overflow-hidden rounded-2xl border border-outline-variant bg-surface shadow-2xl shadow-slate-950/30"
                    :class="[getModalWidthClasses(activeModal.size), getModalHeightClasses(activeModal.size)]"
                    role="dialog"
                    aria-modal="true"
                    :key="activeModal.id"
                >
                    <header class="flex items-center justify-between border-b border-outline-variant px-6 py-4">
                        <h2 class="text-lg font-semibold tracking-tight text-on-surface">{{ activeModal.title }}</h2>
                        <button
                            v-if="activeModal.cancelLabel !== null"
                            type="button"
                            class="rounded-lg p-1.5 text-on-surface-variant transition-colors hover:bg-surface-container hover:text-on-surface"
                            :disabled="activeModal.isSubmitting"
                            @click="cancel(activeModal.id)"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </header>

                    <div v-if="activeModal.message" class="px-6 pb-1 pt-4">
                        <p class="text-sm text-on-surface-variant">
                            {{ activeModal.message }}
                        </p>
                    </div>

                    <div v-if="activeModal.type !== 'form' && !activeModal.message" class="pt-1" />

                    <div
                        v-if="activeModal.type === 'form' && activeModal.form"
                        class="space-y-4 overflow-y-auto px-6 pb-3 pt-4"
                    >
                        <div
                            v-if="activeModal.qrSvg"
                            class="rounded-xl border border-outline-variant bg-surface-container-low p-3"
                        >
                            <div class="mx-auto w-fit" v-html="activeModal.qrSvg" />
                        </div>

                        <section
                            v-for="(section, sectionIndex) in getFormSections(activeModal.form.fields)"
                            :key="`${activeModal.id}-section-${section.title || sectionIndex}`"
                            class="pb-2"
                        >
                            <h3 v-if="section.title" class="mb-3 border-b border-outline-variant pb-2 text-base font-semibold text-on-surface">
                                {{ section.title }}
                            </h3>

                            <div class="space-y-3">
                                <label
                                    v-for="field in section.fields"
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

                                    <label
                                        v-else-if="field.type === 'checkbox'"
                                        class="flex items-center gap-2 py-1 text-sm text-on-surface"
                                    >
                                        <input
                                            type="checkbox"
                                            class="h-4 w-4 rounded border-outline-variant accent-primary"
                                            :checked="(activeModal.form.values[field.name] ?? 'false') === 'true'"
                                            @change="onTextInput($event, activeModal.id, field.name)"
                                        >
                                        <span>{{ field.placeholder ?? '' }}</span>
                                    </label>

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
                            </div>
                        </section>

                        <p v-if="activeModal.submitError" class="text-sm font-medium text-red-600">
                            {{ activeModal.submitError }}
                        </p>
                    </div>

                    <footer class="mt-auto px-6 pb-6 pt-2">
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
            </Transition>
        </div>
    </Teleport>
</template>
