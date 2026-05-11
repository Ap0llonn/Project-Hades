import type { ComputedRef } from 'vue';

export type ModalType = 'confirmation' | 'danger' | 'form';
export type ModalSize = 'sm' | 'md' | 'lg';

export type ModalFieldType = 'text' | 'email' | 'password' | 'number' | 'textarea' | 'checkbox';

export interface ModalField {
    name: string;
    label: string;
    section?: string;
    type?: ModalFieldType;
    placeholder?: string;
    required?: boolean;
    autocomplete?: string;
    rows?: number;
    initialValue?: string;
}

export interface ModalActionContext {
    modalId: string;
    dismiss: () => void;
}

export type ModalConfirmHandler = (context: ModalActionContext) => void | Promise<void>;
export type ModalCancelHandler = (context: ModalActionContext) => void;
export type ModalSubmitHandler = (
    values: Record<string, string>,
    context: ModalActionContext,
) => void | Promise<void>;

export interface BaseModalOptions {
    title?: string;
    message?: string;
    qrSvg?: string;
    size?: ModalSize;
    confirmLabel?: string;
    cancelLabel?: string | null;
    disableBackdropClose?: boolean;
    onCancel?: ModalCancelHandler;
}

export interface ActionModalOptions extends BaseModalOptions {
    onConfirm?: ModalConfirmHandler;
}

export interface FormModalOptions extends BaseModalOptions {
    fields: ModalField[];
    onSubmit?: ModalSubmitHandler;
}

export interface ModalFormState {
    fields: ModalField[];
    values: Record<string, string>;
    onSubmit?: ModalSubmitHandler;
}

export interface ModalRecord {
    id: string;
    type: ModalType;
    size: ModalSize;
    title: string;
    message: string;
    qrSvg?: string;
    confirmLabel: string;
    cancelLabel: string | null;
    disableBackdropClose: boolean;
    onConfirm?: ModalConfirmHandler;
    onCancel?: ModalCancelHandler;
    form?: ModalFormState;
    isSubmitting: boolean;
    submitError: string;
    createdAt: number;
}

export interface ModalStoreApi {
    modals: ComputedRef<ModalRecord[]>;
    activeModal: ComputedRef<ModalRecord | null>;
    confirmation: (options: ActionModalOptions) => string;
    danger: (options: ActionModalOptions) => string;
    form: (options: FormModalOptions) => string;
    dismiss: (modalId?: string) => void;
    clear: () => void;
    updateFieldValue: (modalId: string, fieldName: string, value: string) => void;
    submit: (modalId: string) => Promise<void>;
    cancel: (modalId: string) => void;
}

export interface ModalApi {
    confirmation: ModalStoreApi['confirmation'];
    danger: ModalStoreApi['danger'];
    form: ModalStoreApi['form'];
    dismiss: ModalStoreApi['dismiss'];
    clear: ModalStoreApi['clear'];
}
