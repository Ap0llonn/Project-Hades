import { ref } from 'vue';
import { CryptoDecryptor, CryptoEncryptor } from '../../../shared/utils';
import { dekService } from '../../../shared/services/dekService';
import { vaultSession } from '../../shared/ts/VaultSession';
import { vaultServiceApi } from '../services/vaultServiceApi';

const VAULT_UNLOCK_TIMEOUT_MS = 6000;

const toApiType = (itemType) => (itemType === 'card' ? 'credit_card' : itemType);
const toUiType = (apiType) => (apiType === 'credit_card' ? 'card' : apiType);

export const useDashboardServices = () => {
    const passwords = ref([]);
    const visiblePasswords = ref(new Set());
    const serviceRecordsById = ref(new Map());
    const cachedPrivateKeyBase64 = ref(null);

    const toBase64FromBytes = (bytes) => {
        let binary = '';
        bytes.forEach((byte) => {
            binary += String.fromCharCode(byte);
        });

        return btoa(binary);
    };

    const passwordStrength = (password) => {
        if (password.length < 8) {
            return 'weak';
        }

        const hasLower = /[a-z]/.test(password);
        const hasUpper = /[A-Z]/.test(password);
        const hasDigit = /\d/.test(password);
        const hasSymbol = /[^A-Za-z0-9]/.test(password);

        return hasLower && hasUpper && hasDigit && hasSymbol ? 'strong' : 'medium';
    };

    const waitForUnlockedVault = async () => {
        if (vaultSession.isUnlocked()) {
            return;
        }

        const startAt = Date.now();
        while (!vaultSession.isUnlocked()) {
            if (Date.now() - startAt >= VAULT_UNLOCK_TIMEOUT_MS) {
                throw new Error('Vault is locked. Please sign in again to unlock your DEK.');
            }

            await new Promise((resolve) => {
                window.setTimeout(resolve, 120);
            });
        }
    };

    const formatLastUsed = (isoDate) => {
        if (typeof isoDate !== 'string' || isoDate.trim() === '') {
            return 'just now';
        }

        const parsed = new Date(isoDate);
        if (Number.isNaN(parsed.getTime())) {
            return 'just now';
        }

        return parsed.toLocaleString();
    };

    const normalizePayloadForEncryption = (item) => ({
        name: (item.name ?? '').trim(),
        type: item.type ?? 'note',
        username: (item.username ?? '').trim(),
        password: item.type === 'login' ? (item.password ?? '') : '',
        url: (item.url ?? '').trim(),
        cardholder: (item.cardholder ?? '').trim(),
        cardNumber: (item.cardNumber ?? '').trim(),
        expiry: (item.expiry ?? '').trim(),
        cvc: (item.cvc ?? '').trim(),
        note: (item.note ?? '').trim(),
        requireMasterPassword: Boolean(item.requireMasterPassword),
        fullName: (item.fullName ?? '').trim(),
        email: (item.email ?? '').trim(),
        phone: (item.phone ?? '').trim(),
        address: (item.address ?? '').trim(),
    });

    const decryptServicePayloadRecord = async (record) => {
        const payload = record?.payload;
        const ciphertextBase64 = payload?.ciphertextBase64;
        const ivBase64 = payload?.ivBase64;
        if (typeof ciphertextBase64 !== 'string' || typeof ivBase64 !== 'string') {
            throw new Error('Service payload is invalid.');
        }

        const decryptedJson = await vaultSession.decryptPassword({ ciphertextBase64, ivBase64 });
        const parsed = JSON.parse(decryptedJson);

        if (!parsed || typeof parsed !== 'object') {
            throw new Error('Service payload is invalid.');
        }

        return parsed;
    };

    const buildUiItemFromRecord = async (record) => {
        try {
            const itemType = toUiType(record.type ?? 'note');
            const item = await decryptServicePayloadRecord(record);
            const cardLastFour = String(item.cardNumber ?? '').replace(/\s+/g, '').slice(-4);
            const identityLabel = String(item.email ?? '').trim() || String(item.fullName ?? '').trim() || 'Identity';

            return {
                id: String(record.id),
                name: String(item.name ?? 'Encrypted item').trim() || 'Encrypted item',
                username:
                    itemType === 'login'
                        ? String(item.username ?? '').trim()
                        : itemType === 'card'
                            ? `**** **** **** ${cardLastFour || '0000'}`
                            : itemType === 'identity'
                                ? identityLabel
                                : 'Secure note',
                password: itemType === 'login' ? String(item.password ?? '') : '***',
                url: itemType === 'login' ? String(item.url ?? '').trim() : '',
                category: itemType,
                favorite: Boolean(record.favorite),
                note: String(item.note ?? '').trim(),
                requiresMasterPasswordForNote: Boolean(item.requireMasterPassword),
                lastUsed: formatLastUsed(record.updated_at),
                strength: itemType === 'login' ? passwordStrength(String(item.password ?? '')) : 'strong',
                status: String(record.status ?? 'active'),
            };
        } catch {
            return null;
        }
    };

    const loadServices = async () => {
        await waitForUnlockedVault();
        const records = await vaultServiceApi.list();
        serviceRecordsById.value = new Map(records.map((record) => [String(record.id), record]));
        const mapped = await Promise.all(records.map((record) => buildUiItemFromRecord(record)));
        passwords.value = mapped.filter((item) => item !== null);
    };

    const createService = async (item) => {
        await waitForUnlockedVault();

        const plainPayload = normalizePayloadForEncryption(item);
        const encryptedPayload = await CryptoEncryptor.encryptWithKey(
            JSON.stringify(plainPayload),
            vaultSession.getDek(),
        );

        const created = await vaultServiceApi.create({
            type: toApiType(item.type ?? 'note'),
            favorite: Boolean(item.favorite),
            status: 'active',
            payload: {
                ...encryptedPayload,
                version: 1,
                algorithm: 'libsodium.crypto_secretbox',
                encoding: 'json',
                schema: 1,
                createdAt: new Date().toISOString(),
            },
        });

        const newItem = await buildUiItemFromRecord(created);
        serviceRecordsById.value.set(String(created.id), created);
        if (newItem !== null) {
            passwords.value.unshift(newItem);
        }

        return newItem;
    };

    const deleteService = async (id) => {
        await vaultServiceApi.remove(String(id));
        serviceRecordsById.value.delete(String(id));
        passwords.value = passwords.value.filter((item) => String(item.id) !== String(id));
    };

    const getServiceForEdit = async (id) => {
        await waitForUnlockedVault();

        const serviceId = String(id);
        const record = serviceRecordsById.value.get(serviceId);
        if (!record) {
            throw new Error('Service not found.');
        }

        const payload = await decryptServicePayloadRecord(record);

        return {
            id: serviceId,
            type: toUiType(record.type ?? 'note'),
            status: String(record.status ?? 'active'),
            favorite: Boolean(record.favorite),
            name: String(payload.name ?? '').trim(),
            username: String(payload.username ?? '').trim(),
            password: String(payload.password ?? ''),
            url: String(payload.url ?? '').trim(),
            cardholder: String(payload.cardholder ?? '').trim(),
            cardNumber: String(payload.cardNumber ?? '').trim(),
            expiry: String(payload.expiry ?? '').trim(),
            cvc: String(payload.cvc ?? '').trim(),
            note: String(payload.note ?? '').trim(),
            requireMasterPassword: Boolean(payload.requireMasterPassword),
            fullName: String(payload.fullName ?? '').trim(),
            email: String(payload.email ?? '').trim(),
            phone: String(payload.phone ?? '').trim(),
            address: String(payload.address ?? '').trim(),
        };
    };

    const updateService = async (id, item) => {
        await waitForUnlockedVault();

        const serviceId = String(id);
        const currentRecord = serviceRecordsById.value.get(serviceId);
        if (!currentRecord) {
            throw new Error('Service not found.');
        }

        const plainPayload = normalizePayloadForEncryption(item);
        const encryptedPayload = await CryptoEncryptor.encryptWithKey(
            JSON.stringify(plainPayload),
            vaultSession.getDek(),
        );

        const createdAt = typeof currentRecord?.payload?.createdAt === 'string'
            && currentRecord.payload.createdAt.trim() !== ''
            ? currentRecord.payload.createdAt
            : new Date().toISOString();

        const updatedRecord = await vaultServiceApi.update(serviceId, {
            type: toApiType(item.type ?? toUiType(currentRecord.type ?? 'note')),
            favorite: Boolean(item.favorite),
            status: item.status === 'archived' ? 'archived' : 'active',
            payload: {
                ...encryptedPayload,
                version: 1,
                algorithm: 'libsodium.crypto_secretbox',
                encoding: 'json',
                schema: 1,
                createdAt,
            },
        });

        serviceRecordsById.value.set(String(updatedRecord.id), updatedRecord);
        const nextUiItem = await buildUiItemFromRecord(updatedRecord);
        if (nextUiItem !== null) {
            passwords.value = passwords.value.map((entry) =>
                String(entry.id) === String(nextUiItem.id) ? nextUiItem : entry,
            );
        }

        return nextUiItem;
    };

    const toggleFavorite = async (item) => {
        const nextFavorite = !Boolean(item.favorite);
        await vaultServiceApi.update(String(item.id), {
            favorite: nextFavorite,
        });

        item.favorite = nextFavorite;
    };

    const togglePasswordVisibility = (id) => {
        const next = new Set(visiblePasswords.value);
        if (next.has(id)) {
            next.delete(id);
        } else {
            next.add(id);
        }
        visiblePasswords.value = next;
    };

    const lookupShareRecipient = async (email) => vaultServiceApi.lookupRecipientPublicKey(String(email ?? '').trim());

    const shareService = async (serviceId, recipientEmail, keyEnvelope) => vaultServiceApi.share(String(serviceId), {
        recipient_email: String(recipientEmail ?? '').trim(),
        key_envelope: keyEnvelope ?? {},
    });

    const loadIncomingShares = async (params = {}) => vaultServiceApi.listIncomingShares(params);

    const revokeShare = async (serviceId, shareId) => {
        await vaultServiceApi.revokeShare(String(serviceId), String(shareId));
    };

    const unwrapPrivateKeyForSharing = async () => {
        await waitForUnlockedVault();

        if (typeof cachedPrivateKeyBase64.value === 'string' && cachedPrivateKeyBase64.value.trim() !== '') {
            return cachedPrivateKeyBase64.value;
        }

        const bootstrap = await dekService.fetchBootstrap();
        const wrapper = bootstrap?.wrapped_private_key && typeof bootstrap.wrapped_private_key === 'object'
            ? bootstrap.wrapped_private_key
            : null;

        const normalizedWrapper = wrapper?.wrapped_private_key && typeof wrapper.wrapped_private_key === 'object'
            ? wrapper.wrapped_private_key
            : wrapper;

        const ciphertextBase64 = typeof normalizedWrapper?.ciphertext === 'string'
            ? normalizedWrapper.ciphertext
            : (typeof normalizedWrapper?.ciphertextBase64 === 'string' ? normalizedWrapper.ciphertextBase64 : '');
        const ivBase64 = typeof normalizedWrapper?.iv === 'string'
            ? normalizedWrapper.iv
            : (typeof normalizedWrapper?.ivBase64 === 'string' ? normalizedWrapper.ivBase64 : '');

        if (ciphertextBase64 === '' || ivBase64 === '') {
            throw new Error('Unable to load private key wrapper for sharing.');
        }

        const privateKeyBase64 = await vaultSession.decryptPassword({
            ciphertextBase64,
            ivBase64,
        });

        cachedPrivateKeyBase64.value = privateKeyBase64;
        return privateKeyBase64;
    };

    const createShareEnvelope = async (recipientPublicKeyBase64) => {
        await waitForUnlockedVault();

        const recipientPublicKey = String(recipientPublicKeyBase64 ?? '').trim();
        if (recipientPublicKey === '') {
            throw new Error('Recipient public key is required.');
        }

        const ownerPrivateKeyBase64 = await unwrapPrivateKeyForSharing();
        const ownerDekBase64 = toBase64FromBytes(vaultSession.getDek());

        const envelope = await CryptoEncryptor.encryptWithRecipientPublicAndSenderPrivateKey(
            ownerDekBase64,
            recipientPublicKey,
            ownerPrivateKeyBase64,
        );

        return {
            ...envelope,
            version: 1,
            schema: 1,
        };
    };

    const decryptIncomingShareEnvelope = async (keyEnvelope) => {
        if (!keyEnvelope || typeof keyEnvelope !== 'object') {
            return null;
        }

        const algorithm = String(keyEnvelope.algorithm ?? '');
        const ciphertextBase64 = String(keyEnvelope.ciphertextBase64 ?? '');

        if (algorithm !== 'libsodium.crypto_box_easy') {
            return null;
        }

        const ivBase64 = String(keyEnvelope.ivBase64 ?? '');
        const senderPublicKeyBase64 = String(keyEnvelope.senderPublicKeyBase64 ?? '');
        if (!ivBase64 || !senderPublicKeyBase64 || !ciphertextBase64) {
            return null;
        }

        const recipientPrivateKeyBase64 = await unwrapPrivateKeyForSharing();

        return CryptoDecryptor.decryptWithSenderPublicKey(
            {
                ciphertextBase64,
                ivBase64,
            },
            senderPublicKeyBase64,
            recipientPrivateKeyBase64,
        );
    };

    const decryptSharedServiceData = async (encryptedPayload, sharedDekBase64) => {
        if (!encryptedPayload || typeof encryptedPayload !== 'object') {
            return null;
        }

        const ciphertextBase64 = String(encryptedPayload.ciphertextBase64 ?? '');
        const ivBase64 = String(encryptedPayload.ivBase64 ?? '');

        if (!ciphertextBase64 || !ivBase64) {
            return null;
        }

        const decryptedJson = await CryptoDecryptor.decryptPasswordWithDek(
            { ciphertextBase64, ivBase64 },
            sharedDekBase64,
        );

        const parsed = JSON.parse(decryptedJson);
        return parsed && typeof parsed === 'object' ? parsed : null;
    };

    return {
        passwords,
        visiblePasswords,
        passwordStrength,
        loadServices,
        createService,
        deleteService,
        getServiceForEdit,
        updateService,
        toggleFavorite,
        togglePasswordVisibility,
        lookupShareRecipient,
        createShareEnvelope,
        decryptIncomingShareEnvelope,
        decryptSharedServiceData,
        shareService,
        loadIncomingShares,
        revokeShare,
    };
};
