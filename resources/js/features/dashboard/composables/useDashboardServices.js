import { ref } from 'vue';
import { CryptoEncryptor } from '../../../shared/utils';
import { vaultSession } from '../../shared/ts/VaultSession';
import { vaultServiceApi } from '../services/vaultServiceApi';

const VAULT_UNLOCK_TIMEOUT_MS = 6000;

const toApiType = (itemType) => (itemType === 'card' ? 'credit_card' : itemType);
const toUiType = (apiType) => (apiType === 'credit_card' ? 'card' : apiType);

export const useDashboardServices = () => {
    const passwords = ref([]);
    const visiblePasswords = ref(new Set());

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

    const buildUiItemFromRecord = async (record) => {
        try {
            const payload = record?.payload;
            const ciphertextBase64 = payload?.ciphertextBase64;
            const ivBase64 = payload?.ivBase64;
            if (typeof ciphertextBase64 !== 'string' || typeof ivBase64 !== 'string') {
                return null;
            }

            const decryptedJson = await vaultSession.decryptPassword({ ciphertextBase64, ivBase64 });
            const item = JSON.parse(decryptedJson);
            const itemType = toUiType(record.type ?? 'note');
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
        if (newItem !== null) {
            passwords.value.unshift(newItem);
        }

        return newItem;
    };

    const deleteService = async (id) => {
        await vaultServiceApi.remove(String(id));
        passwords.value = passwords.value.filter((item) => String(item.id) !== String(id));
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

    return {
        passwords,
        visiblePasswords,
        passwordStrength,
        loadServices,
        createService,
        deleteService,
        toggleFavorite,
        togglePasswordVisibility,
    };
};

