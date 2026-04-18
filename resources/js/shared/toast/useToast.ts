import type { ToastApi } from './types';
import { useToastStore } from './toastStore';

export const useToast = (): ToastApi => {
    const {
        pushToast,
        confirmation,
        danger,
        form,
        removeToast,
        clearToasts,
    } = useToastStore();

    return {
        pushToast,
        confirmation,
        danger,
        form,
        dismiss: removeToast,
        clear: clearToasts,
    };
};
