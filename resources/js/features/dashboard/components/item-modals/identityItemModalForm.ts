import type { ItemModalOpener } from './types';

export const openIdentityItemModal: ItemModalOpener = (modal, onSave) => {
    modal.form({
        title: 'Add Identity',
        size: 'lg',
        confirmLabel: 'Save Item',
        fields: [
            {
                name: 'name',
                label: 'Item Name',
                section: 'Item Details',
                placeholder: 'Primary identity',
                required: true,
            },
            {
                name: 'fullName',
                label: 'Full Name',
                section: 'Personal Information',
                placeholder: 'Jane Doe',
            },
            {
                name: 'email',
                label: 'Email',
                section: 'Personal Information',
                type: 'email',
                placeholder: 'jane@example.com',
            },
            {
                name: 'phone',
                label: 'Phone',
                section: 'Personal Information',
                placeholder: '+1 555 555 5555',
            },
            {
                name: 'address',
                label: 'Address',
                section: 'Personal Information',
                type: 'textarea',
                placeholder: 'Street, city, country',
                rows: 3,
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
            if (values.fullName.trim() === '' && values.email.trim() === '') {
                throw new Error('Provide at least a full name or an email.');
            }

            return onSave({
                type: 'identity',
                name: values.name,
                fullName: values.fullName,
                email: values.email,
                phone: values.phone,
                address: values.address,
                note: values.note,
                requireMasterPassword: values.requireMasterPassword === 'true',
            });
        },
    });
};

