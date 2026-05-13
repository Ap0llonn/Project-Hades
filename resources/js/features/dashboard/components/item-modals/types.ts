import type { ModalApi } from '../../../../shared/modal';

export interface ItemModalPayload {
    type: 'login' | 'card' | 'note' | 'identity';
    name: string;
    username?: string;
    password?: string;
    url?: string;
    cardholder?: string;
    cardNumber?: string;
    expiry?: string;
    cvc?: string;
    note?: string;
    requireMasterPassword?: boolean;
    fullName?: string;
    email?: string;
    phone?: string;
    address?: string;
    favorite?: boolean;
}

export type ItemModalSaveHandler = (payload: ItemModalPayload) => Promise<void> | void;

export type ItemModalOpener = (
    modal: ModalApi,
    onSave: ItemModalSaveHandler,
) => void;
