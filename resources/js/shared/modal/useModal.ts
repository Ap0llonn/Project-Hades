import type { ModalApi } from './types';
import { useModalStore } from './modalStore';

export const useModal = (): ModalApi => {
    const {
        confirmation,
        danger,
        form,
        dismiss,
        clear,
    } = useModalStore();

    return {
        confirmation,
        danger,
        form,
        dismiss,
        clear,
    };
};
