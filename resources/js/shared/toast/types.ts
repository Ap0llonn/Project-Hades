import type { ComputedRef } from 'vue';

export type ToastType = 'confirmation' | 'danger' | 'form';

export type ToastFormFieldType = 'text' | 'email' | 'password' | 'number' | 'textarea';

export interface ToastFormField {
    name: string;
    label: string;
    type?: ToastFormFieldType;
    placeholder?: string;
    required?: boolean;
    autocomplete?: string;
    rows?: number;
    initialValue?: string;
}

export interface FormToastSubmitContext {
    toastId: string;
    dismiss: () => void;
}

export type FormToastSubmitHandler = (
    values: Record<string, string>,
    context: FormToastSubmitContext,
) => void | Promise<void>;

export type FormToastCancelHandler = (context: FormToastSubmitContext) => void;

export interface ToastFormState {
    fields: ToastFormField[];
    values: Record<string, string>;
    submitLabel: string;
    cancelLabel: string;
    onSubmit?: FormToastSubmitHandler;
    onCancel?: FormToastCancelHandler;
    isSubmitting: boolean;
    submitError: string;
}

export interface ToastRecord {
    id: string;
    type: ToastType;
    title: string;
    message: string;
    form?: ToastFormState;
    createdAt: number;
}

export interface PushToastInput {
    type?: ToastType;
    title?: string;
    message?: string;
    durationMs?: number;
    form?: ToastFormState;
}

export interface ToastOptions {
    title?: string;
    durationMs?: number;
}

export interface FormToastOptions extends ToastOptions {
    message?: string;
    fields: ToastFormField[];
    submitLabel?: string;
    cancelLabel?: string;
    onSubmit?: FormToastSubmitHandler;
    onCancel?: FormToastCancelHandler;
}

export interface ToastStoreApi {
    toasts: ComputedRef<ToastRecord[]>;
    pushToast: (input: PushToastInput) => string;
    confirmation: (message: string, options?: ToastOptions) => string;
    danger: (message: string, options?: ToastOptions) => string;
    form: (options: FormToastOptions) => string;
    removeToast: (toastId: string) => void;
    clearToasts: () => void;
    updateFormFieldValue: (toastId: string, fieldName: string, value: string) => void;
    submitFormToast: (toastId: string) => Promise<void>;
    cancelFormToast: (toastId: string) => void;
}

export interface ToastApi {
    pushToast: ToastStoreApi['pushToast'];
    confirmation: ToastStoreApi['confirmation'];
    danger: ToastStoreApi['danger'];
    form: ToastStoreApi['form'];
    dismiss: ToastStoreApi['removeToast'];
    clear: ToastStoreApi['clearToasts'];
}
