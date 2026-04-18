import { computed, reactive } from 'vue';
import type {
    FormToastOptions,
    FormToastSubmitContext,
    PushToastInput,
    ToastOptions,
    ToastFormField,
    ToastRecord,
    ToastStoreApi,
    ToastType,
} from './types';

const DEFAULT_DURATION_MS = 5000;
const MAX_VISIBLE_TOASTS = 4;

const state = reactive<{ toasts: ToastRecord[] }>({
    toasts: [],
});

let nextToastId = 1;
const activeTimers = new Map<string, ReturnType<typeof window.setTimeout>>();

const clearTimer = (toastId: string): void => {
    const timer = activeTimers.get(toastId);
    if (!timer) {
        return;
    }

    window.clearTimeout(timer);
    activeTimers.delete(toastId);
};

const removeToast = (toastId: string): void => {
    clearTimer(toastId);
    state.toasts = state.toasts.filter((toast) => toast.id !== toastId);
};

const scheduleDismiss = (toastId: string, durationMs: number): void => {
    if (durationMs <= 0) {
        return;
    }

    const timer = window.setTimeout(() => {
        removeToast(toastId);
    }, durationMs);
    activeTimers.set(toastId, timer);
};

const pushToast = ({
    type = 'confirmation',
    title = '',
    message = '',
    durationMs = DEFAULT_DURATION_MS,
    form,
}: PushToastInput = {}): string => {
    const id = `toast-${nextToastId}`;
    nextToastId += 1;

    const toast: ToastRecord = {
        id,
        type,
        title,
        message,
        form,
        createdAt: Date.now(),
    };

    const nextToasts = [toast, ...state.toasts];
    const droppedToasts = nextToasts.slice(MAX_VISIBLE_TOASTS);
    droppedToasts.forEach((droppedToast) => clearTimer(droppedToast.id));
    state.toasts = nextToasts.slice(0, MAX_VISIBLE_TOASTS);
    scheduleDismiss(id, durationMs);

    return id;
};

const clearToasts = (): void => {
    state.toasts.forEach((toast) => clearTimer(toast.id));
    state.toasts = [];
};

const confirmation = (message: string, options: ToastOptions = {}): string =>
    pushToast({
        type: 'confirmation',
        title: options.title ?? 'Success',
        message,
        durationMs: options.durationMs ?? DEFAULT_DURATION_MS,
    });

const danger = (message: string, options: ToastOptions = {}): string =>
    pushToast({
        type: 'danger',
        title: options.title ?? 'Action failed',
        message,
        durationMs: options.durationMs ?? DEFAULT_DURATION_MS,
    });

const getInitialValues = (fields: ToastFormField[]): Record<string, string> => {
    const values: Record<string, string> = {};
    fields.forEach((field) => {
        values[field.name] = field.initialValue ?? '';
    });
    return values;
};

const form = (options: FormToastOptions): string =>
    pushToast({
        type: 'form',
        title: options.title ?? 'Quick form',
        message: options.message ?? '',
        durationMs: options.durationMs ?? 0,
        form: {
            fields: options.fields,
            values: getInitialValues(options.fields),
            submitLabel: options.submitLabel ?? 'Submit',
            cancelLabel: options.cancelLabel ?? 'Cancel',
            onSubmit: options.onSubmit,
            onCancel: options.onCancel,
            isSubmitting: false,
            submitError: '',
        },
    });

const getFormToast = (toastId: string): ToastRecord | undefined =>
    state.toasts.find((toast) => toast.id === toastId && toast.type === 'form' && !!toast.form);

const updateFormFieldValue = (toastId: string, fieldName: string, value: string): void => {
    const toast = getFormToast(toastId);
    if (!toast || !toast.form) {
        return;
    }

    toast.form.values[fieldName] = value;
};

const getMissingRequiredField = (toast: ToastRecord): string | null => {
    if (!toast.form) {
        return null;
    }

    const missingField = toast.form.fields.find((field) => {
        if (!field.required) {
            return false;
        }

        const value = toast.form?.values[field.name] ?? '';
        return value.trim().length === 0;
    });

    if (!missingField) {
        return null;
    }

    return missingField.label;
};

const cancelFormToast = (toastId: string): void => {
    const toast = getFormToast(toastId);
    if (!toast || !toast.form) {
        return;
    }

    const context: FormToastSubmitContext = {
        toastId,
        dismiss: () => removeToast(toastId),
    };

    toast.form.onCancel?.(context);
    removeToast(toastId);
};

const submitFormToast = async (toastId: string): Promise<void> => {
    const toast = getFormToast(toastId);
    if (!toast || !toast.form) {
        return;
    }

    if (toast.form.isSubmitting) {
        return;
    }

    const missingRequiredLabel = getMissingRequiredField(toast);
    if (missingRequiredLabel) {
        toast.form.submitError = `${missingRequiredLabel} is required.`;
        return;
    }

    toast.form.submitError = '';
    toast.form.isSubmitting = true;

    const context: FormToastSubmitContext = {
        toastId,
        dismiss: () => removeToast(toastId),
    };

    try {
        if (toast.form.onSubmit) {
            await toast.form.onSubmit({ ...toast.form.values }, context);
        }

        if (state.toasts.some((currentToast) => currentToast.id === toastId)) {
            removeToast(toastId);
        }
    } catch (error) {
        const currentToast = getFormToast(toastId);
        if (currentToast?.form) {
            currentToast.form.submitError = error instanceof Error
                ? error.message
                : 'Unable to submit the form.';
        }
    } finally {
        const currentToast = getFormToast(toastId);
        if (currentToast?.form) {
            currentToast.form.isSubmitting = false;
        }
    }
};

export const useToastStore = (): ToastStoreApi => ({
    toasts: computed(() => state.toasts),
    pushToast,
    confirmation,
    danger,
    form,
    removeToast,
    clearToasts,
    updateFormFieldValue,
    submitFormToast,
    cancelFormToast,
});

export type { ToastRecord, ToastType };
