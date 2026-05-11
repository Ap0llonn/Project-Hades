import type { ItemModalOpener } from './types';

export const openLoginItemModal: ItemModalOpener = (modal, onSave) => {
    modal.form({
        title: 'Add Login',
        size: 'lg',
        confirmLabel: 'Save Item',
        fields: [
            {
                name: 'name',
                label: 'Item Name',
                section: 'Item Details',
                placeholder: 'Primary login',
                required: true,
            },
            {
                name: 'username',
                label: 'Username',
                section: 'Login Credentials',
                placeholder: 'john.doe@email.com',
                required: true,
            },
            {
                name: 'password',
                label: 'Password',
                section: 'Login Credentials',
                type: 'password',
                placeholder: 'Password',
                required: true,
            },
            {
                name: 'url',
                label: 'Website (URL)',
                section: 'Autofill Options',
                placeholder: 'example.com',
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
            onSave({
                type: 'login',
                name: values.name,
                username: values.username,
                password: values.password,
                url: values.url,
                note: values.note,
                requireMasterPassword: values.requireMasterPassword === 'true',
            });
        },
    });
};
