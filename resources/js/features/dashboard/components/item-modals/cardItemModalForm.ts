import type { ItemModalOpener } from './types';

export const openCardItemModal: ItemModalOpener = (modal, onSave) => {
    modal.form({
        title: 'Add Credit Card',
        size: 'lg',
        confirmLabel: 'Save Item',
        fields: [
            {
                name: 'name',
                label: 'Item Name',
                section: 'Item Details',
                placeholder: 'Primary card',
                required: true,
            },
            {
                name: 'cardholder',
                label: 'Cardholder',
                section: 'Payment Information',
                placeholder: 'Cardholder name',
                required: true,
            },
            {
                name: 'cardNumber',
                label: 'Card Number',
                section: 'Payment Information',
                placeholder: '**** **** **** 0000',
                required: true,
            },
            {
                name: 'expiry',
                label: 'Expiry',
                section: 'Payment Information',
                placeholder: 'MM/YY',
            },
            {
                name: 'cvc',
                label: 'CVC',
                section: 'Payment Information',
                placeholder: '***',
            },
            {
                name: 'note',
                label: 'Note',
                section: 'Note',
                type: 'textarea',
                rows: 3,
                placeholder: 'Add an optional note',
            },
            {
                name: 'requireMasterPassword',
                label: 'Note Protection',
                section: 'Note',
                type: 'checkbox',
                placeholder: 'Require master password again to view this note',
                initialValue: 'false',
            },
        ],
        onSubmit: (values) => {
            return onSave({
                type: 'card',
                name: values.name,
                cardholder: values.cardholder,
                cardNumber: values.cardNumber,
                expiry: values.expiry,
                cvc: values.cvc,
                note: values.note,
                requireMasterPassword: values.requireMasterPassword === 'true',
            });
        },
    });
};

