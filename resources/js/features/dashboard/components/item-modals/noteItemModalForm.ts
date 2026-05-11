import type { ItemModalOpener } from './types';

export const openNoteItemModal: ItemModalOpener = (modal, onSave) => {
    modal.form({
        title: 'Add Secure Note',
        size: 'lg',
        confirmLabel: 'Save Item',
        fields: [
            {
                name: 'name',
                label: 'Item Name',
                section: 'Item Details',
                placeholder: 'Note title',
                required: true,
            },
            {
                name: 'note',
                label: 'Content',
                section: 'Secure Note',
                type: 'textarea',
                placeholder: 'Write your note',
                rows: 5,
                required: true,
            },
            {
                name: 'requireMasterPassword',
                label: 'Note Protection',
                section: 'Secure Note',
                type: 'checkbox',
                placeholder: 'Require master password again to view this note',
                initialValue: 'false',
            },
        ],
        onSubmit: (values) => {
            onSave({
                type: 'note',
                name: values.name,
                note: values.note,
                requireMasterPassword: values.requireMasterPassword === 'true',
            });
        },
    });
};
