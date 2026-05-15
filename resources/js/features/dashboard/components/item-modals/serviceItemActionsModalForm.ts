import type { ModalApi, ModalField } from '../../../../shared/modal';
import type { ItemModalPayload } from './types';

export interface EditableServiceItem extends ItemModalPayload {
    id: string;
    status?: 'active' | 'archived';
}

interface ServiceItemActionsHandlers {
    onSave: (payload: EditableServiceItem) => Promise<void> | void;
    onDelete: (item: EditableServiceItem) => Promise<void> | void;
}

const buildFields = (item: EditableServiceItem): ModalField[] => {
    const fields: ModalField[] = [
        {
            name: 'name',
            label: 'Item Name',
            section: 'Item Details',
            placeholder: 'Service name',
            required: true,
            initialValue: String(item.name ?? ''),
        },
        {
            name: 'favorite',
            label: 'Favorites',
            section: 'Item Details',
            type: 'checkbox',
            placeholder: 'Mark as favorite',
            initialValue: item.favorite ? 'true' : 'false',
        },
    ];

    if (item.type === 'login') {
        fields.push(
            {
                name: 'username',
                label: 'Username',
                section: 'Login Credentials',
                placeholder: 'john.doe@email.com',
                required: true,
                initialValue: String(item.username ?? ''),
            },
            {
                name: 'password',
                label: 'Password',
                section: 'Login Credentials',
                type: 'password',
                placeholder: 'Password',
                required: true,
                initialValue: String(item.password ?? ''),
            },
            {
                name: 'url',
                label: 'Website (URL)',
                section: 'Autofill Options',
                placeholder: 'example.com',
                initialValue: String(item.url ?? ''),
            },
        );
    }

    if (item.type === 'card') {
        fields.push(
            {
                name: 'cardholder',
                label: 'Cardholder',
                section: 'Payment Information',
                placeholder: 'Cardholder name',
                required: true,
                initialValue: String(item.cardholder ?? ''),
            },
            {
                name: 'cardNumber',
                label: 'Card Number',
                section: 'Payment Information',
                placeholder: '**** **** **** 0000',
                required: true,
                initialValue: String(item.cardNumber ?? ''),
            },
            {
                name: 'expiry',
                label: 'Expiry',
                section: 'Payment Information',
                placeholder: 'MM/YY',
                initialValue: String(item.expiry ?? ''),
            },
            {
                name: 'cvc',
                label: 'CVC',
                section: 'Payment Information',
                placeholder: '***',
                initialValue: String(item.cvc ?? ''),
            },
        );
    }

    if (item.type === 'identity') {
        fields.push(
            {
                name: 'fullName',
                label: 'Full Name',
                section: 'Personal Information',
                placeholder: 'Jane Doe',
                initialValue: String(item.fullName ?? ''),
            },
            {
                name: 'email',
                label: 'Email',
                section: 'Personal Information',
                type: 'email',
                placeholder: 'jane@example.com',
                initialValue: String(item.email ?? ''),
            },
            {
                name: 'phone',
                label: 'Phone',
                section: 'Personal Information',
                placeholder: '+1 555 555 5555',
                initialValue: String(item.phone ?? ''),
            },
            {
                name: 'address',
                label: 'Address',
                section: 'Personal Information',
                type: 'textarea',
                rows: 3,
                placeholder: 'Street, city, country',
                initialValue: String(item.address ?? ''),
            },
        );
    }

    fields.push({
        name: 'note',
        label: item.type === 'note' ? 'Content' : 'Note',
        section: item.type === 'note' ? 'Secure Note' : 'Note',
        type: 'textarea',
        rows: item.type === 'note' ? 5 : 3,
        placeholder: item.type === 'note' ? 'Write your note' : 'Add an optional note',
        required: item.type === 'note',
        initialValue: String(item.note ?? ''),
    });

    fields.push({
        name: 'requireMasterPassword',
        label: 'Note Protection',
        section: item.type === 'note' ? 'Secure Note' : 'Note',
        type: 'checkbox',
        placeholder: 'Require master password again to view this note',
        initialValue: item.requireMasterPassword ? 'true' : 'false',
    });

    fields.push({
        name: 'deleteService',
        label: 'Danger Zone',
        section: 'Danger Zone',
        type: 'checkbox',
        placeholder: 'Delete this service permanently',
        initialValue: 'false',
    });

    return fields;
};

export const openServiceItemActionsModal = (
    modal: ModalApi,
    item: EditableServiceItem,
    handlers: ServiceItemActionsHandlers,
): void => {
    modal.form({
        title: 'Edit Service',
        size: 'lg',
        confirmLabel: 'Apply Changes',
        message: 'Update this service. To delete it permanently, enable the Danger Zone option and submit.',
        fields: buildFields(item),
        onSubmit: async (values) => {
            const shouldDelete = values.deleteService === 'true';
            if (shouldDelete) {
                await handlers.onDelete(item);
                return;
            }

            if (item.type === 'identity' && values.fullName.trim() === '' && values.email.trim() === '') {
                throw new Error('Provide at least a full name or an email.');
            }

            const payload: EditableServiceItem = {
                ...item,
                name: values.name,
                favorite: values.favorite === 'true',
                note: values.note,
                requireMasterPassword: values.requireMasterPassword === 'true',
            };

            if (item.type === 'login') {
                payload.username = values.username;
                payload.password = values.password;
                payload.url = values.url;
            }

            if (item.type === 'card') {
                payload.cardholder = values.cardholder;
                payload.cardNumber = values.cardNumber;
                payload.expiry = values.expiry;
                payload.cvc = values.cvc;
            }

            if (item.type === 'identity') {
                payload.fullName = values.fullName;
                payload.email = values.email;
                payload.phone = values.phone;
                payload.address = values.address;
            }

            await handlers.onSave(payload);
        },
    });
};
