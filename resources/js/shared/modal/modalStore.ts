import { computed, reactive } from 'vue';
import type {
    ActionModalOptions,
    FormModalOptions,
    ModalActionContext,
    ModalField,
    ModalRecord,
    ModalStoreApi,
    ModalType,
} from './types';

const state = reactive<{ modals: ModalRecord[] }>({
    modals: [],
});

let nextModalId = 1;

const getDefaultTitle = (type: ModalType): string => {
    if (type === 'danger') {
        return 'Confirm dangerous action';
    }
    if (type === 'form') {
        return 'Complete form';
    }

    return 'Confirm action';
};

const getDefaultConfirmLabel = (type: ModalType): string => {
    if (type === 'danger') {
        return 'Continue';
    }
    if (type === 'form') {
        return 'Submit';
    }

    return 'Confirm';
};

const getInitialValues = (fields: ModalField[]): Record<string, string> => {
    const values: Record<string, string> = {};
    fields.forEach((field) => {
        values[field.name] = field.initialValue ?? '';
    });
    return values;
};

const pushModal = (
    type: ModalType,
    options: ActionModalOptions | FormModalOptions,
): string => {
    const id = `modal-${nextModalId}`;
    nextModalId += 1;

    const modal: ModalRecord = {
        id,
        type,
        title: options.title ?? getDefaultTitle(type),
        message: options.message ?? '',
        confirmLabel: options.confirmLabel ?? getDefaultConfirmLabel(type),
        cancelLabel: options.cancelLabel === undefined ? 'Cancel' : options.cancelLabel,
        disableBackdropClose: options.disableBackdropClose ?? false,
        onConfirm: 'onConfirm' in options ? options.onConfirm : undefined,
        onCancel: options.onCancel,
        form: 'fields' in options
            ? {
                fields: options.fields,
                values: getInitialValues(options.fields),
                onSubmit: options.onSubmit,
            }
            : undefined,
        isSubmitting: false,
        submitError: '',
        createdAt: Date.now(),
    };

    state.modals.push(modal);
    return id;
};

const getModal = (modalId?: string): ModalRecord | undefined => {
    if (!modalId) {
        return state.modals[state.modals.length - 1];
    }

    return state.modals.find((modal) => modal.id === modalId);
};

const dismiss = (modalId?: string): void => {
    const targetModal = getModal(modalId);
    if (!targetModal) {
        return;
    }

    state.modals = state.modals.filter((modal) => modal.id !== targetModal.id);
};

const clear = (): void => {
    state.modals = [];
};

const confirmation = (options: ActionModalOptions): string =>
    pushModal('confirmation', options);

const danger = (options: ActionModalOptions): string =>
    pushModal('danger', options);

const form = (options: FormModalOptions): string =>
    pushModal('form', options);

const updateFieldValue = (modalId: string, fieldName: string, value: string): void => {
    const modal = getModal(modalId);
    if (!modal || !modal.form) {
        return;
    }

    modal.form.values[fieldName] = value;
};

const getMissingRequiredField = (modal: ModalRecord): string | null => {
    if (!modal.form) {
        return null;
    }

    const missingField = modal.form.fields.find((field) => {
        if (!field.required) {
            return false;
        }

        const value = modal.form?.values[field.name] ?? '';
        return value.trim().length === 0;
    });

    return missingField?.label ?? null;
};

const cancel = (modalId: string): void => {
    const modal = getModal(modalId);
    if (!modal || modal.isSubmitting) {
        return;
    }

    const context: ModalActionContext = {
        modalId,
        dismiss: () => dismiss(modalId),
    };

    modal.onCancel?.(context);
    dismiss(modalId);
};

const submit = async (modalId: string): Promise<void> => {
    const modal = getModal(modalId);
    if (!modal || modal.isSubmitting) {
        return;
    }

    if (modal.form) {
        const missingRequiredLabel = getMissingRequiredField(modal);
        if (missingRequiredLabel) {
            modal.submitError = `${missingRequiredLabel} is required.`;
            return;
        }
    }

    modal.submitError = '';
    modal.isSubmitting = true;

    const context: ModalActionContext = {
        modalId,
        dismiss: () => dismiss(modalId),
    };

    try {
        if (modal.form?.onSubmit) {
            await modal.form.onSubmit({ ...modal.form.values }, context);
        } else if (modal.onConfirm) {
            await modal.onConfirm(context);
        }

        if (getModal(modalId)) {
            dismiss(modalId);
        }
    } catch (error) {
        const currentModal = getModal(modalId);
        if (!currentModal) {
            return;
        }

        if (currentModal.form) {
            currentModal.submitError = error instanceof Error
                ? error.message
                : 'Unable to submit this form.';
        }
    } finally {
        const currentModal = getModal(modalId);
        if (currentModal) {
            currentModal.isSubmitting = false;
        }
    }
};

export const useModalStore = (): ModalStoreApi => ({
    modals: computed(() => state.modals),
    activeModal: computed(() => state.modals[state.modals.length - 1] ?? null),
    confirmation,
    danger,
    form,
    dismiss,
    clear,
    updateFieldValue,
    submit,
    cancel,
});

export type { ModalRecord, ModalType };
